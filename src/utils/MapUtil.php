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
 * Map（关联数组）工具类
 */
class MapUtil
{
    /**
     * 是否为空
     */
    public static function isEmpty(?array $map): bool
    {
        return $map === null || $map === [];
    }

    /**
     * 是否不为空
     */
    public static function isNotEmpty(?array $map): bool
    {
        return !static::isEmpty($map);
    }

    /**
     * 快速创建 Map
     */
    public static function of(mixed ...$pairs): array
    {
        $result = [];
        $keys = array_keys($pairs);
        for ($i = 0; $i < count($keys); $i += 2) {
            $result[$keys[$i]] = $keys[$i + 1] ?? null;
        }
        return $result;
    }

    /**
     * 从键值数组对创建 Map
     *
     * @param array $keys
     * @param array $values
     * @return array
     */
    public static function ofEntries(array $keys, array $values): array
    {
        return array_combine($keys, $values);
    }

    /**
     * 安全获取值
     */
    public static function get(array $map, string|int $key, mixed $default = null): mixed
    {
        return $map[$key] ?? $default;
    }

    /**
     * 安全获取字符串
     */
    public static function getStr(array $map, string $key, string $default = ''): string
    {
        return (string)($map[$key] ?? $default);
    }

    /**
     * 安全获取整数
     */
    public static function getInt(array $map, string $key, int $default = 0): int
    {
        return NumberUtil::parseInt($map[$key] ?? $default, $default);
    }

    /**
     * 安全获取浮点数
     */
    public static function getFloat(array $map, string $key, float $default = 0.0): float
    {
        return NumberUtil::parseFloat($map[$key] ?? $default, $default);
    }

    /**
     * 安全获取布尔值
     */
    public static function getBool(array $map, string $key, bool $default = false): bool
    {
        $value = $map[$key] ?? null;
        if ($value === null) {
            return $default;
        }
        return (bool)$value;
    }

    /**
     * 安全获取数组
     */
    public static function getArr(array $map, string $key, array $default = []): array
    {
        $value = $map[$key] ?? null;
        return is_array($value) ? $value : $default;
    }

    /**
     * 若 key 不存在则设置并返回
     */
    public static function getOrPut(array &$map, string $key, callable $supplier): mixed
    {
        if (!array_key_exists($key, $map)) {
            $map[$key] = $supplier();
        }
        return $map[$key];
    }

    /**
     * 过滤 Map
     */
    public static function filter(array $map, callable $predicate): array
    {
        return array_filter($map, $predicate, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 过滤并转换
     */
    public static function filterAndMap(
        array $map,
        callable $filterPredicate,
        callable $mapper
    ): array {
        $result = [];
        foreach ($map as $key => $value) {
            if ($filterPredicate($value, $key)) {
                $result[$key] = $mapper($value, $key);
            }
        }
        return $result;
    }

    /**
     * 映射 Map 的值
     */
    public static function map(array $map, callable $mapper): array
    {
        $result = [];
        foreach ($map as $key => $value) {
            $result[$key] = $mapper($value, $key);
        }
        return $result;
    }

    /**
     * 映射 Map 的键
     */
    public static function mapKeys(array $map, callable $mapper): array
    {
        $result = [];
        foreach ($map as $key => $value) {
            $result[$mapper($key, $value)] = $value;
        }
        return $result;
    }

    /**
     * 按 value 排序
     */
    public static function sortByValue(array $map, int $order = SORT_ASC, int $flags = SORT_REGULAR): array
    {
        $result = $map;
        $order === SORT_ASC ? asort($result, $flags) : arsort($result, $flags);
        return $result;
    }

    /**
     * 按 key 排序
     */
    public static function sortByKey(array $map, int $order = SORT_ASC, int $flags = SORT_REGULAR): array
    {
        $result = $map;
        $order === SORT_ASC ? ksort($result, $flags) : krsort($result, $flags);
        return $result;
    }

    /**
     * 翻转键值
     */
    public static function flip(array $map): array
    {
        return array_flip($map);
    }

    /**
     * 合并多个 Map（后者覆盖前者）
     */
    public static function merge(array ...$maps): array
    {
        return array_merge(...$maps);
    }

    /**
     * 深度合并
     */
    public static function mergeRecursive(array ...$maps): array
    {
        $result = [];
        foreach ($maps as $map) {
            foreach ($map as $key => $value) {
                if (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = static::mergeRecursive($result[$key], $value);
                } else {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * 取指定 keys
     */
    public static function only(array $map, array $keys): array
    {
        return array_intersect_key($map, array_flip($keys));
    }

    /**
     * 排除指定 keys
     */
    public static function except(array $map, array $keys): array
    {
        return array_diff_key($map, array_flip($keys));
    }

    /**
     * 移除 null 值
     */
    public static function removeNullValues(array $map): array
    {
        return array_filter($map, fn($v) => $v !== null);
    }

    /**
     * 获取所有 keys
     */
    public static function keys(array $map): array
    {
        return array_keys($map);
    }

    /**
     * 获取所有 values
     */
    public static function values(array $map): array
    {
        return array_values($map);
    }

    /**
     * 是否包含指定 key
     */
    public static function containsKey(array $map, string|int $key): bool
    {
        return array_key_exists($key, $map);
    }

    /**
     * 是否包含指定 value
     */
    public static function containsValue(array $map, mixed $value): bool
    {
        return in_array($value, $map, true);
    }

    /**
     * Map 转查询字符串
     */
    public static function toQueryString(array $map): string
    {
        return http_build_query($map);
    }

    /**
     * 查询字符串转 Map
     */
    public static function parseQueryString(string $queryString): array
    {
        parse_str($queryString, $result);
        return $result;
    }

    /**
     * 分组
     *
     * @param array $array 元素数组
     * @param callable $keyExtractor 分组键提取
     * @return array<string, array>
     */
    public static function groupBy(array $array, callable $keyExtractor): array
    {
        $result = [];
        foreach ($array as $item) {
            $key = $keyExtractor($item);
            $result[$key][] = $item;
        }
        return $result;
    }

    /**
     * 二维 Map 扁平化
     *
     * ['a' => ['b' => 1, 'c' => 2]] => ['a.b' => 1, 'a.c' => 2]
     */
    public static function flat(array $map, string $delimiter = '.'): array
    {
        $result = [];
        static::doFlat($map, '', $result, $delimiter);
        return $result;
    }

    private static function doFlat(array $map, string $prefix, array &$result, string $delimiter): void
    {
        foreach ($map as $key => $value) {
            $fullKey = $prefix === '' ? (string)$key : $prefix . $delimiter . $key;
            if (is_array($value)) {
                static::doFlat($value, $fullKey, $result, $delimiter);
            } else {
                $result[$fullKey] = $value;
            }
        }
    }

    /**
     * 扁平 Map 还原为嵌套
     *
     * ['a.b' => 1, 'a.c' => 2] => ['a' => ['b' => 1, 'c' => 2]]
     */
    public static function unflat(array $map, string $delimiter = '.'): array
    {
        $result = [];
        foreach ($map as $key => $value) {
            $keys = explode($delimiter, $key);
            $current = &$result;
            foreach ($keys as $i => $k) {
                if ($i === count($keys) - 1) {
                    $current[$k] = $value;
                } else {
                    if (!isset($current[$k]) || !is_array($current[$k])) {
                        $current[$k] = [];
                    }
                    $current = &$current[$k];
                }
            }
        }
        return $result;
    }

    /**
     * 两个数组打包成 Map
     */
    public static function zip(array $keys, array $values): array
    {
        return array_combine($keys, $values);
    }

    /**
     * Map 转为 Entry 列表
     *
     * ['a' => 1, 'b' => 2] => [['key' => 'a', 'value' => 1], ...]
     */
    public static function toEntries(array $map): array
    {
        $result = [];
        foreach ($map as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
        }
        return $result;
    }
}
