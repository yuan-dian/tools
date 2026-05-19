<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/19
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\http\constant;

/**
 * HTTP 请求方法常量
 */
final class HttpMethod
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const PATCH = 'PATCH';
    public const HEAD = 'HEAD';
    public const OPTIONS = 'OPTIONS';
    public const TRACE = 'TRACE';
    public const CONNECT = 'CONNECT';

    /** 不允许携带 body 的方法 */
    public const SAFE_BODY_EXCLUDED = [self::GET, self::HEAD];

    private function __construct()
    {
    }

    /**
     * 是否为允许携带 body 的方法
     */
    public static function allowsBody(string $method): bool
    {
        return !in_array(strtoupper($method), self::SAFE_BODY_EXCLUDED, true);
    }

    /**
     * 校验方法是否合法
     */
    public static function isValid(string $method): bool
    {
        return in_array(strtoupper($method), [
            self::GET,
            self::POST,
            self::PUT,
            self::DELETE,
            self::PATCH,
            self::HEAD,
            self::OPTIONS,
            self::TRACE,
            self::CONNECT,
        ], true);
    }
}