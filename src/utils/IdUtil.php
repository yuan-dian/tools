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
 * ID 生成工具类
 */
class IdUtil
{
    /**
     * 生成简单 UUID（无横线）
     */
    public static function simpleUUID(): string
    {
        return str_replace('-', '', static::fastUUID());
    }

    /**
     * 生成 UUID（带横线）
     */
    public static function fastUUID(): string
    {
        return UUIDUtil::fastUUID();
    }

    /**
     * 生成 ObjectId（兼容 MongoDB）
     *
     * 4字节时间戳 + 5字节随机值 + 3字节自增计数器
     */
    public static function objectId(): string
    {
        static $counter = 0;
        $timestamp = (int)sprintf('%08x', time());
        $random = bin2hex(random_bytes(5));
        $counter = ($counter + 1) & 0xffffff;
        $counterHex = sprintf('%06x', $counter);
        return $timestamp . $random . $counterHex;
    }

    /**
     * 随机数 ID
     */
    public static function randomId(int $length = 10): string
    {
        return RandomUtil::randomNumbers($length);
    }

    /**
     * 生成雪花 ID
     */
    public static function snowflakeId(): int
    {
        return SnowflakeUtil::nextId();
    }


}