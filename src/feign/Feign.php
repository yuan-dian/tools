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
    // 全局默认映射配置
    private static ResponseMapping $responseMapping;

    /**
     * 设置默认远程响应格式
     * @param ResponseMapping $mapping
     * @date 2026/5/21 下午12:03
     * @author 原点 467490186@qq.com
     */
    public static function setResponseMapping(ResponseMapping $mapping): void
    {
        self::$responseMapping = $mapping;
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
        );
    }
}