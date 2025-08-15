<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/8/15
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;


class UUIDUtil
{
    /**
     * 生成随机UUID (版本4)
     * @return string 36字符的标准UUID格式
     */
    public static function randomUUID(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * 生成简化的UUID (无连字符)
     * @return string 32字符的UUID
     * @throws \Random\RandomException
     * @date 2025/8/15 下午2:28
     * @author 原点 467490186@qq.com
     */
    public static function simpleUUID(): string {
        return bin2hex(random_bytes(16));
    }

    /**
     * 基于名称的UUID (版本3)
     * @param string $namespace 命名空间UUID
     * @param string $name 名称
     * @return string 生成的UUID
     */
    public static function nameUUIDFromString(string $namespace, string $name): string {
        $nhex = str_replace(['-','{','}'], '', $namespace);
        $nstr = '';

        for($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }

        $hash = md5($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    /**
     * 快速生成UUID (性能优化版)
     * @return string 36字符的标准UUID格式
     * @throws \Random\RandomException
     * @date 2025/8/15 下午2:29
     * @author 原点 467490186@qq.com
     */
    public static function fastUUID(): string {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // 设置版本为4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // 设置变体

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}