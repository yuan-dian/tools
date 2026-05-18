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
 * 字符集工具类
 */
class CharsetUtil
{
    /** 常用字符集 */
    public const UTF_8 = 'UTF-8';
    public const GBK = 'GBK';
    public const GB2312 = 'GB2312';
    public const ISO_8859_1 = 'ISO-8859-1';
    public const ASCII = 'ASCII';

    /**
     * 转换字符集
     */
    public static function convert(string $str, string $from, string $to): string
    {
        if ($from === $to) {
            return $str;
        }
        return mb_convert_encoding($str, $to, $from);
    }

    /**
     * 转为 UTF-8
     */
    public static function toUtf8(string $str, string $from = self::GBK): string
    {
        return static::convert($str, $from, self::UTF_8);
    }

    /**
     * 转为 GBK
     */
    public static function toGbk(string $str, string $from = self::UTF_8): string
    {
        return static::convert($str, $from, self::GBK);
    }

    /**
     * 检测字符集
     */
    public static function detect(string $str): ?string
    {
        $detected = mb_detect_encoding(
            $str,
            [self::UTF_8, self::GBK, self::GB2312, self::ISO_8859_1, self::ASCII],
            true
        );
        return $detected ?: null;
    }

    /**
     * 是否为 UTF-8
     */
    public static function isUtf8(string $str): bool
    {
        return mb_check_encoding($str, self::UTF_8);
    }

    /**
     * 是否为 GBK
     */
    public static function isGbk(string $str): bool
    {
        return mb_check_encoding($str, self::GBK);
    }

    /**
     * 获取系统默认字符集
     */
    public static function getDefaultCharset(): string
    {
        return mb_internal_encoding();
    }

    /**
     * 设置系统默认字符集
     */
    public static function setDefaultCharset(string $charset): bool
    {
        return mb_internal_encoding($charset);
    }

    /**
     * 字符串长度（考虑多字节）
     */
    public static function length(string $str, ?string $charset = null): int
    {
        return mb_strlen($str, $charset);
    }

    /**
     * 截取字符串（考虑多字节）
     */
    public static function sub(string $str, int $start, ?int $length = null, ?string $charset = null): string
    {
        return mb_substr($str, $start, $length, $charset);
    }

    /**
     * 添加 BOM 头
     */
    public static function addBom(string $str): string
    {
        return "\xEF\xBB\xBF" . $str;
    }

    /**
     * 移除 BOM 头
     */
    public static function removeBom(string $str): string
    {
        if (str_starts_with($str, "\xEF\xBB\xBF")) {
            return substr($str, 3);
        }
        return $str;
    }
}
