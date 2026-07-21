<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/21
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\feign;

use yuandian\Tools\bean\BeanUtil;
use yuandian\Tools\http\constant\Option;
use yuandian\Tools\http\HttpClient;
use yuandian\Tools\reflection\ClassReflector;
use yuandian\Tools\reflection\MethodReflector;

class FeignProxy
{
    private static ?HttpClient $sharedClient = null;

    public function __construct(
        private readonly string $interfaceClass,
        private readonly ResponseMapping $responseMapping,
        private readonly array $staticMap,
        private readonly ?\Closure $serviceResolver = null,
    ) {
    }

    public function __call(string $method, array $args = []): mixed
    {
        $ref = new ClassReflector($this->interfaceClass);

        // 1. 解析 #[FeignClient] 类注解
        $clientAttr = $ref->getAttribute(FeignClient::class);
        $basePath = rtrim($clientAttr->path, '/');
        $serviceName = $clientAttr->name;

        // 2. 解析方法注解
        $methodRef = $ref->getMethod($method);
        $route = $methodRef->getAttribute(FeignRoute::class);
        if (!$route) {
            throw new \BadMethodCallException("方法 [{$method}] 缺少路由注解");
        }

        // 3. 解析参数
        $parsedParams = $this->parseParams($methodRef, $args, $route->path);
        $body = $parsedParams['body'] ?? [];
        $fullPathTemplate = $parsedParams['fullPath'] ?? $route->path;

        // 4. 发起请求（带重试）
        $maxRetries = $route->retries;
        return $this->executeWithRetry(function () use (
            $serviceName,
            $basePath,
            $fullPathTemplate,
            $route,
            $body,
            $methodRef,
            $ref,
            $args
        ) {
            try {
                $baseUrl = $this->resolveBaseUrl($serviceName);
                $fullPath = $baseUrl . $basePath . $fullPathTemplate;
                // 设置超时时间
                $body[Option::TIMEOUT] = $route->timeout;

                $response = $this->getHttpClient()->request($route->method, $fullPath, $body);

                return $this->wrapResult($response->getBody(), $serviceName, $methodRef, $ref);
            } catch (\Throwable $e) {
                return $this->handleFallback($ref, $methodRef, $args, $e);
            }
        }, $maxRetries);
    }

    /**
     * 获取共享 HttpClient 实例（连接池复用）
     */
    private function getHttpClient(): HttpClient
    {
        return self::$sharedClient ??= new HttpClient();
    }

    /**
     * 从 Nacos 或静态表解析 baseUrl
     */
    private function resolveBaseUrl(string $serviceName): string
    {
        // 1. 优先静态注册
        if (isset($this->staticMap[$serviceName])) {
            return $this->staticMap[$serviceName];
        }

        // 2. 使用自定义服务解析器（Nacos）
        if ($this->serviceResolver) {
            return ($this->serviceResolver)($serviceName);
        }

        throw new \RuntimeException("服务 [{$serviceName}] 未配置地址且未初始化 Nacos");
    }

    /**
     * 带重试的执行
     */
    private function executeWithRetry(callable $fn, int $maxRetries = 0): mixed
    {
        $lastException = null;
        for ($i = 0; $i <= $maxRetries; $i++) {
            try {
                return $fn();
            } catch (\Throwable $e) {
                $lastException = $e;
                // 业务异常不重试
                if ($e instanceof FeignException || $i === $maxRetries) {
                    throw $e;
                }
                // 网络异常重试
                usleep(100 * 1000); // 100ms 延迟
            }
        }
        throw $lastException;
    }

    /**
     * 优先级：方法级注解 > 类级注解 > 全局默认
     */
    private function resolveResponseMapping(
        MethodReflector $methodRef,
        ClassReflector $classRef,
    ): ResponseMapping {
        // 方法级
        $methodAttrs = $methodRef->getAttribute(ResponseMapping::class);
        if ($methodAttrs) {
            return $methodAttrs;
        }

        // 类级
        $classAttrs = $classRef->getAttribute(ResponseMapping::class);
        if ($classAttrs) {
            return $classAttrs;
        }
        return $this->responseMapping;
    }

    /**
     * 解析方法参数上的注解
     */
    private function parseParams(MethodReflector $ref, array $args, string $routePath): array
    {
        $data = ['body' => [], 'fullPath' => $routePath];

        foreach ($ref->getParameters() as $i => $param) {
            $value = $args[$i] ?? null;

            // #[RequestParam] - 查询参数
            if ($attr = $param->getAttribute(RequestParam::class)) {
                $key = $attr->name ?? $param->getName();
                $data['body'][$attr->bodyType][$key] = $value;
            }

            // #[RequestBody] - 请求体
            if ($attr = $param->getAttribute(RequestBody::class)) {
                $data['body'][$attr->bodyType] = array_merge(
                    $data['body'][$attr->bodyType] ?? [],
                    BeanUtil::objectToArray($value)
                );
            }

            // #[RequestHeader] - 自定义 Header
            if ($attr = $param->getAttribute(RequestHeader::class)) {
                $key = $attr->name ?? $param->getName();
                $data['body'][Option::HEADERS][$key] = $value;
            }

            // #[PathVariable] - 路径参数
            if ($attr = $param->getAttribute(PathVariable::class)) {
                $key = $attr->name ?? $param->getName();
                $data['fullPath'] = str_replace('{' . $key . '}', (string)$value, $data['fullPath']);
            }

            // #[QueryMap] - 对象展开为 query
            if ($attr = $param->getAttribute(QueryMap::class)) {
                $data['body'][Option::QUERY] = array_merge(
                    $data['body'][Option::QUERY] ?? [],
                    BeanUtil::objectToArray($value)
                );
            }
        }

        return $data;
    }

    /**
     * 反序列化
     */
    private function wrapResult(
        string $response,
        string $serviceName,
        MethodReflector $methodRef,
        ClassReflector $classRef,
    ): mixed {
        $data = json_decode($response, true);
        // 1. 解析成功码
        $responseCode = $this->resolveResponseMapping($methodRef, $classRef);

        // 2. 判断是否成功
        $code = $data[$responseCode->codeName] ?? -1;
        $payload = $data[$responseCode->bodyName] ?? null;

        if ($code !== $responseCode->successCode) {
            throw new FeignException(
                serviceName: $serviceName,
                method: $methodRef->getName(),
                remoteCode: $code,
                remoteMessage: $data[$responseCode->messageName] ?? '',
                remoteData: $payload,
                rawResponse: $response,
            );
        }

        $returnType = $methodRef->getReturnType();
        $typeName = $returnType?->getName() ?? 'mixed';

        if ($typeName === 'void') {
            return null;
        }

        if ($typeName === 'mixed') {
            return $payload;
        }

        if ($typeName === 'int' || $typeName === 'integer') {
            return (int)$payload;
        }
        if ($typeName === 'float' || $typeName === 'double') {
            return (float)$payload;
        }
        if ($typeName === 'string') {
            return (string)$payload;
        }
        if ($typeName === 'bool' || $typeName === 'boolean') {
            return (bool)$payload;
        }

        if ($typeName === 'array' && $rtAttr = $methodRef->getAttribute(ReturnType::class)) {
            $typeName = $rtAttr->className;
        }
        if (class_exists($typeName)) {
            return BeanUtil::copyProperties((array)$payload, $typeName);
        }
        return $payload;
    }

    /**
     * 降级处理
     */
    private function handleFallback(
        ClassReflector $classRef,
        MethodReflector $methodRef,
        array $args,
        \Throwable $e,
    ): mixed {
        // 自定义异常处理
        if ($classRef->hasMethod('create')) {
            $factory = $classRef->getMethod('create')->invoke(null, $e);
            if (method_exists($factory, $methodRef->getName())) {
                return $factory->{$methodRef->getName()}(...$args);
            }
        }
        throw $e;
    }
}