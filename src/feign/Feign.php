<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/20
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\feign;

use yuandian\Tools\bean\BeanUtil;
use yuandian\Tools\http\HttpClient;
use yuandian\Tools\reflection\ClassReflector;
use yuandian\Tools\reflection\MethodReflector;

class Feign
{
    public static array $staticMap = [];
    // 全局默认成功码
    public static int $globalSuccessCode = 0;

    /**
     * 静态注册（不走 Nacos 时用）
     */
    public static function registerService(string $name, string $url): void
    {
        self::$staticMap[$name] = rtrim($url, '/');
    }

    /**
     * 创建代理客户端（核心）
     *
     * @template T
     * @param class-string<T> $interfaceClass
     * @return T
     */
    public static function create(string $interfaceClass): object
    {
        return new class($interfaceClass) {
            private string $interfaceClass;
            private array $staticMap;

            public function __construct(string $interfaceClass)
            {
                $this->interfaceClass = $interfaceClass;
                $this->staticMap = Feign::$staticMap;
            }

            public function __call(string $method, array $args): mixed
            {
                $ref = new ClassReflector($this->interfaceClass);

                // 1. 解析 #[FeignClient] 类注解
                $clientAttr = $ref->getAttribute(FeignClient::class);
                $basePath = rtrim($clientAttr->path, '/');
                $serviceName = $clientAttr->name;

                // 2. 解析方法注解 #[GetMapping] / #[PostMapping]
                $methodRef = $ref->getMethod($method);

                $route = $methodRef->getAttribute(FeignRoute::class);
                if (!$route) {
                    throw new \BadMethodCallException("方法 [{$method}] 缺少路由注解");
                }
                $body = $this->parseParams($methodRef, $args);

                // 4. 发起请求（带降级）
                try {
                    $baseUrl = $this->resolveBaseUrl($serviceName);

                    $fullPath = $baseUrl . $basePath . $route->path;
                    $http = new HttpClient([
                        'timeout' => 5,
                        'headers' => ['Accept' => 'application/json'],
                    ]);
                    $response = $http->request($route->method, $fullPath, $body);

                    return $this->wrapResult($response->getBody(), $serviceName, $methodRef, $ref);
                } catch (\Throwable $e) {
                    return $this->handleFallback($ref, $methodRef, $args, $e);
                }
            }

            /**
             * 从 Nacos 或静态表解析 baseUrl
             */
            private function resolveBaseUrl(string $serviceName): string
            {
                // 优先静态
                if (isset($this->staticMap[$serviceName])) {
                    return $this->staticMap[$serviceName];
                }

                throw new \RuntimeException("服务 [{$serviceName}] 未配置地址且未初始化 Nacos");
            }

            /**
             * 优先级：方法级注解 > 类级注解 > 全局默认
             */
            private function resolveResponseCode(
                MethodReflector $methodRef,
                ClassReflector $classRef,
            ): mixed {
                // 方法级
                $methodAttrs = $methodRef->getAttribute(ResponseCode::class);
                if ($methodAttrs) {
                    return $methodAttrs->success;
                }

                // 类级
                $classAttrs = $classRef->getAttribute(ResponseCode::class);
                if ($classAttrs) {
                    return $classAttrs->success;
                }
                return Feign::$globalSuccessCode;
            }

            /**
             * 解析方法参数上的注解
             */
            private function parseParams(MethodReflector $ref, array $args): array
            {
                $data = [];
                foreach ($ref->getParameters() as $i => $param) {
                    $value = $args[$i] ?? null;
                    if ($attr = $param->getAttribute(RequestParam::class)) {
                        $key = $attr->name ?? $param->getName();
                        $data[$attr->bodyType][$key] = $value;
                    }
                    if ($attr = $param->getAttribute(RequestBody::class)) {
                        $data[$attr->bodyType] = array_merge(
                            $data[$attr->bodyType] ?? [],
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
                $successCode = $this->resolveResponseCode($methodRef, $classRef);

                // 2. 判断是否成功
                $code = $data['code'] ?? -1;
                $payload = $data['data'] ?? null;

                if ($code !== $successCode) {
                    throw new FeignException(
                        serviceName: $serviceName,
                        method: $methodRef->getName(),
                        remoteCode: $code,
                        remoteMessage: $data['message'] ?? '',
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
             * 优先级：
             *   接口中定义了 handleFallback → 调用它
             */
            private function handleFallback(
                ClassReflector $classRef,
                MethodReflector $methodRef,
                array $args,
                \Throwable $e,
            ): mixed {
                // ── 检查接口中是否有 handleFallback 方法 ──
                if ($classRef->hasMethod('handleFallback')) {
                    return $classRef->getMethod('handleFallback')->invoke(
                        null,    // static 方法
                        $methodRef->getName(),
                        $args,
                        $e
                    );
                }
                // 业务异常且没有自定义处理 → 直接抛出（不吞异常）
                if ($e instanceof FeignException) {
                    throw $e;
                }

                // 网络异常 → 按类型自动兜底
                return $this->autoFallback($methodRef, $e);
            }

            private function autoFallback(MethodReflector $methodRef, \Throwable $e): mixed
            {
                $t = $methodRef->getReturnType()?->getName() ?? 'mixed';

                error_log("[Feign] {$methodRef->getName()} 网络异常降级: {$e->getMessage()}");

                return match ($t) {
                    'int', 'integer' => 0,
                    'float', 'double' => 0.0,
                    'string' => '',
                    'bool', 'boolean' => false,
                    'array' => [],
                    default => null,
                };
            }
        };
    }
}