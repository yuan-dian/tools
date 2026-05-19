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
 * HTTP Header 名称常量池
 */
final class Header
{
    // ========================= 通用头 =========================
    public const ACCEPT = 'Accept';
    public const ACCEPT_CHARSET = 'Accept-Charset';
    public const ACCEPT_ENCODING = 'Accept-Encoding';
    public const ACCEPT_LANGUAGE = 'Accept-Language';
    public const AUTHORIZATION = 'Authorization';
    public const CACHE_CONTROL = 'Cache-Control';
    public const CONNECTION = 'Connection';
    public const CONTENT_DISPOSITION = 'Content-Disposition';
    public const CONTENT_ENCODING = 'Content-Encoding';
    public const CONTENT_LENGTH = 'Content-Length';
    public const CONTENT_TYPE = 'Content-Type';
    public const COOKIE = 'Cookie';
    public const DATE = 'Date';
    public const EXPECT = 'Expect';
    public const EXPIRES = 'Expires';
    public const HOST = 'Host';
    public const IF_MODIFIED_SINCE = 'If-Modified-Since';
    public const IF_NONE_MATCH = 'If-None-Match';
    public const LAST_MODIFIED = 'Last-Modified';
    public const LOCATION = 'Location';
    public const ORIGIN = 'Origin';
    public const PRAGMA = 'Pragma';
    public const RANGE = 'Range';
    public const REFERER = 'Referer';
    public const RETRY_AFTER = 'Retry-After';
    public const SERVER = 'Server';
    public const SET_COOKIE = 'Set-Cookie';
    public const TRANSFER_ENCODING = 'Transfer-Encoding';
    public const USER_AGENT = 'User-Agent';
    public const VARY = 'Vary';
    public const VIA = 'Via';
    public const WARNING = 'Warning';

    // ========================= 认证相关 =========================
    public const WWW_AUTHENTICATE = 'WWW-Authenticate';
    public const PROXY_AUTH = 'Proxy-Authorization';
    public const PROXY_AUTHENTICATE = 'Proxy-Authenticate';

    // ========================= CORS 跨域 =========================
    public const ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    public const ACCESS_CONTROL_ALLOW_METHODS = 'Access-Control-Allow-Methods';
    public const ACCESS_CONTROL_ALLOW_HEADERS = 'Access-Control-Allow-Headers';
    public const ACCESS_CONTROL_ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials';
    public const ACCESS_CONTROL_EXPOSE_HEADERS = 'Access-Control-Expose-Headers';
    public const ACCESS_CONTROL_MAX_AGE = 'Access-Control-Max-Age';
    public const ACCESS_CONTROL_REQUEST_METHOD = 'Access-Control-Request-Method';
    public const ACCESS_CONTROL_REQUEST_HEADERS = 'Access-Control-Request-Headers';

    // ========================= 安全相关 =========================
    public const STRICT_TRANSPORT_SECURITY = 'Strict-Transport-Security';
    public const X_CONTENT_TYPE_OPTIONS = 'X-Content-Type-Options';
    public const X_FRAME_OPTIONS = 'X-Frame-Options';
    public const X_XSS_PROTECTION = 'X-XSS-Protection';
    public const CONTENT_SECURITY_POLICY = 'Content-Security-Policy';

    // ========================= 自定义代理/追踪 =========================
    public const X_FORWARDED_FOR = 'X-Forwarded-For';
    public const X_FORWARDED_PROTO = 'X-Forwarded-Proto';
    public const X_FORWARDED_HOST = 'X-Forwarded-Host';
    public const X_REAL_IP = 'X-Real-IP';
    public const X_REQUEST_ID = 'X-Request-Id';
    public const X_TRACE_ID = 'X-Trace-Id';
    public const X_POWERED_BY = 'X-Powered-By';

    // ========================= API 相关 =========================
    public const X_API_KEY = 'X-Api-Key';
    public const X_API_VERSION = 'X-Api-Version';
    public const X_RATE_LIMIT = 'X-RateLimit-Limit';
    public const X_RATE_REMAINING = 'X-RateLimit-Remaining';
    public const X_RATE_RESET = 'X-RateLimit-Reset';

    private function __construct()
    {
    }

    /**
     * 标准化 Header 名称（首字母大写，连字符分隔）
     */
    public static function normalize(string $name): string
    {
        return str_replace(' ', '-', ucwords(str_replace(['-', '_'], ' ', strtolower($name))));
    }
}