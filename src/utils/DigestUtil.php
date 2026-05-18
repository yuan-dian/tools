<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/18
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;

/**
 * 摘要/加密工具类
 */
class DigestUtil
{
    /**
     * MD5
     */
    public static function md5(string $data): string
    {
        return md5($data);
    }

    /**
     * MD5 16位
     */
    public static function md5Hex16(string $data): string
    {
        return substr(md5($data), 8, 16);
    }

    /**
     * SHA1
     */
    public static function sha1(string $data): string
    {
        return sha1($data);
    }

    /**
     * SHA256
     */
    public static function sha256(string $data): string
    {
        return hash('sha256', $data);
    }

    /**
     * SHA512
     */
    public static function sha512(string $data): string
    {
        return hash('sha512', $data);
    }

    /**
     * 通用哈希
     */
    public static function digest(string $algo, string $data): string
    {
        return hash($algo, $data);
    }

    /**
     * HMAC-MD5
     */
    public static function hmacMd5(string $data, string $key): string
    {
        return hash_hmac('md5', $data, $key);
    }

    /**
     * HMAC-SHA1
     */
    public static function hmacSha1(string $data, string $key): string
    {
        return hash_hmac('sha1', $data, $key);
    }

    /**
     * HMAC-SHA256
     */
    public static function hmacSha256(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key);
    }

    /**
     * HMAC-SHA512
     */
    public static function hmacSha512(string $data, string $key): string
    {
        return hash_hmac('sha512', $data, $key);
    }

    /**
     * 通用 HMAC
     */
    public static function hmac(string $algo, string $data, string $key): string
    {
        return hash_hmac($algo, $data, $key);
    }

    /**
     * bcrypt 加密
     */
    public static function bcrypt(string $data): string
    {
        return password_hash($data, PASSWORD_BCRYPT);
    }

    /**
     * bcrypt 验证
     */
    public static function bcryptVerify(string $data, string $hash): bool
    {
        return password_verify($data, $hash);
    }

    /**
     * 文件 MD5
     */
    public static function md5File(string $filename): string
    {
        return md5_file($filename);
    }

    /**
     * 文件 SHA256
     */
    public static function sha256File(string $filename): string
    {
        return hash_file('sha256', $filename);
    }

    /**
     * 生成盐值
     */
    public static function generateSalt(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * PBKDF2 密钥派生
     */
    public static function pbkdf2(
        string $password,
        string $salt,
        string $algo = 'sha256',
        int $iterations = 10000,
        int $length = 32
    ): string {
        return hash_pbkdf2($algo, $password, $salt, $iterations, $length, true);
    }
}
