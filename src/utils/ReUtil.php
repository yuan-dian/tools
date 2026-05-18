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
 * 正则工具类
 */
class ReUtil
{
    /**
     * 正则匹配（完整匹配）
     */
    public static function isMatch(string $regex, string $content): bool
    {
        return (bool)preg_match($regex, $content);
    }

    /**
     * 正则包含
     */
    public static function contains(string $regex, string $content): bool
    {
        return (bool)preg_match($regex, $content);
    }

    /**
     * 获取匹配到的第一个子串
     */
    public static function get(string $regex, string $content, int $groupIndex = 0): ?string
    {
        if (preg_match($regex, $content, $matches)) {
            return $matches[$groupIndex] ?? null;
        }
        return null;
    }

    /**
     * 获取第一个分组
     */
    public static function getGroup0(string $regex, string $content): ?string
    {
        return static::get($regex, $content, 0);
    }

    /**
     * 获取第二个分组
     */
    public static function getGroup1(string $regex, string $content): ?string
    {
        return static::get($regex, $content, 1);
    }

    /**
     * 查找所有匹配
     */
    public static function findAll(string $regex, string $content): array
    {
        preg_match_all($regex, $content, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 查找所有匹配（返回分组 0）
     */
    public static function findAllGroup0(string $regex, string $content): array
    {
        return static::findAll($regex, $content);
    }

    /**
     * 查找所有匹配（返回分组 1）
     */
    public static function findAllGroup1(string $regex, string $content): array
    {
        preg_match_all($regex, $content, $matches);
        return $matches[1] ?? [];
    }

    /**
     * 查找所有匹配（返回所有分组）
     */
    public static function findAllGroups(string $regex, string $content): array
    {
        preg_match_all($regex, $content, $matches);
        return $matches;
    }

    /**
     * 替换所有匹配
     */
    public static function replaceAll(string $regex, string $replacement, string $content): string
    {
        return preg_replace($regex, $replacement, $content);
    }

    /**
     * 替换第一个匹配
     */
    public static function replaceFirst(string $regex, string $replacement, string $content): string
    {
        return preg_replace($regex, $replacement, $content, 1);
    }

    /**
     * 替换最后一个匹配
     */
    public static function replaceLast(string $regex, string $replacement, string $content): string
    {
        return preg_replace_callback($regex, function ($matches) use ($replacement, &$content, $regex) {
            // 找到最后一个匹配的位置
            $lastPos = strrpos($content, $matches[0]);
            if ($lastPos !== false) {
                $before = substr($content, 0, $lastPos);
                $after = substr($content, $lastPos + strlen($matches[0]));
                $content = $before . $replacement . $after;
            }
            return $matches[0];
        }, $content);
    }

    /**
     * 使用回调替换
     */
    public static function replaceCallback(string $regex, callable $callback, string $content): string
    {
        return preg_replace_callback($regex, $callback, $content);
    }

    /**
     * 统计匹配次数
     */
    public static function count(string $regex, string $content): int
    {
        preg_match_all($regex, $content, $matches);
        return count($matches[0] ?? []);
    }

    /**
     * 删除第一个匹配
     */
    public static function delFirst(string $regex, string $content): string
    {
        return preg_replace($regex, '', $content, 1);
    }

    /**
     * 删除所有匹配
     */
    public static function delAll(string $regex, string $content): string
    {
        return preg_replace($regex, '', $content);
    }

    /**
     * 提取匹配到的数字
     */
    public static function extractNumbers(string $content): array
    {
        preg_match_all('/\d+/', $content, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 提取匹配到的中文
     */
    public static function extractChinese(string $content): array
    {
        preg_match_all('/[\x{4e00}-\x{9fa5}]+/u', $content, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 提取邮箱
     */
    public static function extractEmails(string $content): array
    {
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $content, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 提取 URL
     */
    public static function extractUrls(string $content): array
    {
        preg_match_all('/https?:\/\/[^\s<>\"\']+/i', $content, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 提取手机号
     */
    public static function extractMobiles(string $content): array
    {
        preg_match_all('/1[3-9]\d{9}/', $content, $matches);
        return $matches[0] ?? [];
    }

    /**
     * 提取IP地址
     */
    public static function extractIPs(string $content): array
    {
        preg_match_all('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $content, $matches);
        return $matches[0] ?? [];
    }
}
