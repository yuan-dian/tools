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

class Feign
{
    private static array $staticMap = [];
    private static ResponseMapping $responseMapping;
    private static ?\Closure $serviceResolver = null;

    /**
     * 设置默认远程响应格式
     */
    public static function setResponseMapping(ResponseMapping $mapping): void
    {
        self::$responseMapping = $mapping;
    }

    /**
     * 设置服务解析器（Nacos 动态服务发现）
     * @param \Closure $resolver function(string $serviceName): ?array
     *   返回格式: ['ip' => '192.168.1.100', 'port' => 8081] 或 null
     */
    public static function setServiceResolver(\Closure $resolver): void
    {
        self::$serviceResolver = $resolver;
    }

    /**
     * 静态注册（不走 Nacos 时用）
     */
    public static function registerService(string $name, string $url): void
    {
        self::$staticMap[$name] = rtrim($url, '/');
    }

    /**
     * 创建代理客户端
     *
     * @template T
     * @param class-string<T> $interfaceClass
     * @return T
     */
    public static function create(string $interfaceClass): object
    {
        return new FeignProxy(
            interfaceClass: $interfaceClass,
            responseMapping: self::$responseMapping ?? new ResponseMapping(),
            staticMap: self::$staticMap,
            serviceResolver: self::$serviceResolver,
        );
    }
}