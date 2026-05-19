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
 * 认证类型常量
 */
final class AuthType
{
    public const BASIC = 'basic';
    public const DIGEST = 'digest';
    public const BEARER = 'bearer';
    public const API_KEY = 'api_key';
    public const NTLM = 'ntlm';
    public const NEGOTIATE = 'negotiate';

    /** 对应 CURLAUTH 常量 */
    public const CURL_BASIC = \CURLAUTH_BASIC;
    public const CURL_DIGEST = \CURLAUTH_DIGEST;
    public const CURL_NTLM = \CURLAUTH_NTLM;
    public const CURL_NEGOTIATE = \CURLAUTH_NEGOTIATE;
    public const CURL_ANY = \CURLAUTH_ANY;
    public const CURL_ANYSAFE = \CURLAUTH_ANYSAFE;

    private function __construct()
    {
    }

    /**
     * AuthType 字符串 → CURLAUTH 常量
     */
    public static function toCurlAuth(string $type): int
    {
        return match (strtolower($type)) {
            self::BASIC => self::CURL_BASIC,
            self::DIGEST => self::CURL_DIGEST,
            self::NTLM => self::CURL_NTLM,
            self::NEGOTIATE => self::CURL_NEGOTIATE,
            default => self::CURL_BASIC,
        };
    }

    /**
     * 构建 Bearer Token 头值
     */
    public static function bearerHeaderValue(string $token): string
    {
        return 'Bearer ' . $token;
    }

    /**
     * 构建 Basic Auth 头值
     */
    public static function basicHeaderValue(string $user, string $pass): string
    {
        return 'Basic ' . base64_encode($user . ':' . $pass);
    }
}