<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/23
// +----------------------------------------------------------------------

namespace yuandian\Tools\utils;

use ArrayAccess;
use InvalidArgumentException;

class ArrUtil
{

    /**
     * 判断数组是否为空 (null/空数组)
     * @param array|null $array
     * @return bool
     * @date 2025/7/23 上午10:52
     * @author 原点 467490186@qq.com
     */
    public static function isEmpty(?array $array): bool
    {
        return $array === null || count($array) === 0;
    }

    /**
     * 判断数组是否非空
     * @param array|null $array
     * @return bool
     * @date 2025/7/23 上午10:52
     * @author 原点 467490186@qq.com
     */
    public static function isNotEmpty(?array $array): bool
    {
        return !self::isEmpty($array);
    }

    /**
     * 检查数组是否包含null元素
     * @param array $array
     * @return bool
     * @date 2025/7/23 上午10:52
     * @author 原点 467490186@qq.com
     */
    public static function hasNull(array $array): bool
    {
        return in_array(null, $array, true);
    }

    /**
     * 检查数组是否包含指定元素
     * @param array $array
     * @param $element
     * @return bool
     * @date 2025/7/23 上午10:53
     * @author 原点 467490186@qq.com
     */
    public static function contains(array $array, $element): bool
    {
        return in_array($element, $array, true);
    }

    /**
     * 查找元素首次出现的索引 (找不到返回-1)
     * @param array $array
     * @param $element
     * @return int
     * @date 2025/7/23 上午10:53
     * @author 原点 467490186@qq.com
     */
    public static function indexOf(array $array, $element): int
    {
        $index = array_search($element, $array, true);
        return $index === false ? -1 : $index;
    }

    /**
     * 查找元素最后一次出现的索引(找不到返回-1)
     * @param array $array
     * @param $element
     * @return int
     * @date 2025/7/23 上午10:53
     * @author 原点 467490186@qq.com
     */
    public static function lastIndexOf(array $array, $element): int
    {
        for ($i = count($array) - 1; $i >= 0; $i--) {
            if ($array[$i] === $element) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * 向数组末尾添加元素
     * @param array $array
     * @param mixed ...$values
     * @return array
     * @date 2025/7/23 上午10:53
     * @author 原点 467490186@qq.com
     */
    public static function append(array $array, mixed ...$values): array
    {
        foreach ($values as $value) {
            $array = [...$array, $value];
        }

        return $array;
    }

    /**
     * 向数组开头添加元素
     *
     * @param array $array
     * @param mixed ...$values
     * @return array
     */
    public static function prepend(array $array, mixed ...$values): array
    {
        foreach (array_reverse($values) as $value) {
            $array = [$value, ...$array];
        }

        return $array;
    }

    /**
     * 在指定位置插入元素 (返回新数组)
     * @param array $array
     * @param int $index
     * @param $element
     * @return array
     * @date 2025/7/23 上午10:53
     * @author 原点 467490186@qq.com
     */
    public static function insert(array $array, int $index, $element): array
    {
        if ($index < 0 || $index > count($array)) {
            throw new \InvalidArgumentException("Index out of bounds: $index");
        }
        return array_merge(
            array_slice($array, 0, $index),
            [$element],
            array_slice($array, $index)
        );
    }

    /**
     * 移除指定位置的元素
     * @param array $array
     * @param int $index
     * @return array
     * @date 2025/7/23 上午10:53
     * @author 原点 467490186@qq.com
     */
    public static function remove(array $array, int $index): array
    {
        if ($index < 0 || $index >= count($array)) {
            throw new \InvalidArgumentException("Index out of bounds: $index");
        }
        array_splice($array, $index, 1);
        return $array;
    }

    /**
     * 移除指定元素 (只移除第一个匹配项)
     * @param array $array
     * @param $element
     * @return array
     * @date 2025/7/23 上午10:54
     * @author 原点 467490186@qq.com
     */
    public static function removeElement(array $array, $element): array
    {
        $index = self::indexOf($array, $element);
        return $index >= 0 ? self::remove($array, $index) : $array;
    }

    /**
     * 反转数组 (返回新数组)
     * @param array $array
     * @return array
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function reverse(array $array): array
    {
        return array_reverse($array);
    }

    /**
     * 截取子数组
     * @param array $array
     * @param int $start
     * @param int|null $length
     * @return array
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function sub(array $array, int $start, ?int $length = null): array
    {
        return array_slice($array, $start, $length);
    }

    /**
     * 将数组连接成字符串
     * @param array $array
     * @param string $separator
     * @return string
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function join(array $array, string $separator = ","): string
    {
        return implode($separator, $array);
    }

    /**
     * 数组去重
     * @param array $array
     * @return array
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function distinct(array $array): array
    {
        return array_values(array_unique($array));
    }

    /**
     * 数组过滤 (使用回调函数)
     * @param array $array
     * @param callable $callback
     * @return array
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function filter(array $array, callable $callback): array
    {
        return array_values(array_filter($array, $callback));
    }

    /**
     * 数组映射 (使用回调函数)
     * @param array $array
     * @param callable $callback
     * @return array
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function map(array $array, callable $callback): array
    {
        return array_map($callback, $array);
    }

    /**
     * 获取数组最小值 (仅适用于数值数组)
     * @param array $array
     * @return mixed|null
     * @date 2025/7/23 上午10:55
     * @author 原点 467490186@qq.com
     */
    public static function min(array $array): mixed
    {
        if (self::isEmpty($array)) {
            return null;
        }
        return min($array);
    }

    /**
     * 获取数组最大值 (仅适用于数值数组)
     * @param array $array
     * @return mixed|null
     * @date 2025/7/23 上午10:56
     * @author 原点 467490186@qq.com
     */
    public static function max(array $array): mixed
    {
        if (self::isEmpty($array)) {
            return null;
        }
        return max($array);
    }

    /**
     * 数组求和 (仅适用于数值数组)
     * @param array $array
     * @return float|int
     * @date 2025/7/23 上午10:56
     * @author 原点 467490186@qq.com
     */
    public static function sum(array $array): float|int
    {
        return array_sum($array);
    }

    /**
     * 调整数组大小 (填充或截断)
     * @param array $array
     * @param int $size
     * @param $fillValue
     * @return array
     * @date 2025/7/23 上午10:56
     * @author 原点 467490186@qq.com
     */
    public static function resize(array $array, int $size, $fillValue = null): array
    {
        $currentSize = count($array);
        if ($size < $currentSize) {
            return array_slice($array, 0, $size);
        } elseif ($size > $currentSize) {
            return array_pad($array, $size, $fillValue);
        }
        return $array;
    }

    /**
     * 生成范围数组 (类似range函数但包含边界)
     * @param int $start
     * @param int $end
     * @param int $step
     * @return array
     * @date 2025/7/23 上午10:56
     * @author 原点 467490186@qq.com
     */
    public static function range(int $start, int $end, int $step = 1): array
    {
        return range($start, $end, $step);
    }

    /**
     * 用指定值填充数组
     * @param int $length
     * @param $value
     * @return array
     * @date 2025/7/23 上午10:57
     * @author 原点 467490186@qq.com
     */
    public static function fill(int $length, $value): array
    {
        return array_fill(0, $length, $value);
    }

    /**
     * 检查两个数组是否相等 (顺序和值都相同)
     * @param array $a
     * @param array $b
     * @return bool
     * @date 2025/7/23 上午10:57
     * @author 原点 467490186@qq.com
     */
    public static function equals(array $a, array $b): bool
    {
        return $a === $b;
    }

    /**
     * 检查数组是否已排序 (升序)
     * @param array $array
     * @return bool
     * @date 2025/7/23 上午10:57
     * @author 原点 467490186@qq.com
     */
    public static function isSorted(array $array): bool
    {
        $sorted = $array;
        sort($sorted);
        return $sorted === $array;
    }

    /**
     * 将二维数组转换为键值对映射
     * @param array $array
     * @param string $keyField
     * @param string $valueField
     * @return array
     * @date 2025/7/23 上午10:57
     * @author 原点 467490186@qq.com
     */
    public static function toMap(array $array, string $keyField, string $valueField): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && isset($item[$keyField], $item[$valueField])) {
                $result[$item[$keyField]] = $item[$valueField];
            }
        }
        return $result;
    }


    /**
     * 确定给定值是否可通过数组访问。
     * @param mixed $value
     * @return bool
     * @date 2025/7/23 上午11:30
     * @author 原点 467490186@qq.com
     */
    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }


    /**
     * 将一个数组分成两个数组。一个带有键，另一个带有值。
     * @param array $array
     * @return array
     * @date 2025/7/23 上午11:31
     * @author 原点 467490186@qq.com
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * 展平带有点的多维关联数组。
     * @param array $array
     * @param string $prepend
     * @return array
     * @date 2025/7/23 上午11:31
     * @author 原点 467490186@qq.com
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * 获取除指定键数组之外的所有给定数组
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     * @date 2025/7/23 上午11:31
     * @author 原点 467490186@qq.com
     */
    public static function except(array $array, array|string $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * 判断给定的键是否存在于提供的数组中
     *
     * @param ArrayAccess|array $array
     * @param int|string $key
     * @return bool
     * @date 2025/7/23 上午11:31
     * @author 原点 467490186@qq.com
     */
    public static function exists(ArrayAccess|array $array, int|string $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }



    /**
     * 使用 “.”表示法从给定数组中删除一个或多个数组项。
     *
     * @param array $array
     * @param array|string $keys
     * @date 2025/7/23 上午11:31
     * @author 原点 467490186@qq.com
     */
    public static function forget(array &$array, array|string $keys): void
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * 使用 “.”表示法检查数组中是否存在一个或多个项目
     *
     * @param ArrayAccess|array $array
     * @param array|string $keys
     * @return bool
     * @date 2025/7/23 上午11:32
     * @author 原点 467490186@qq.com
     */
    public static function has(ArrayAccess|array $array, array|string $keys): bool
    {
        $keys = (array) $keys;

        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 从给定数组中获取项目的子集。
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     * @date 2025/7/23 上午11:33
     * @author 原点 467490186@qq.com
     */
    public static function only(array $array, array|string $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * 从数组中获取一个或指定数量的随机值。
     *
     * @param array $array
     * @param int|null $number
     * @return mixed
     * @date 2025/7/23 上午11:41
     * @author 原点 467490186@qq.com
     */
    public static function random(array $array, int $number = null): mixed
    {
        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ($number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }
    /**
     * 打乱给定的数组并返回结果
     *
     * @param array $array
     * @param int|null $seed
     * @return array
     * @date 2025/7/23 上午11:41
     * @author 原点 467490186@qq.com
     */
    public static function shuffle(array $array, ?int $seed = null): array
    {
        if (is_null($seed)) {
            shuffle($array);
        } else {
            srand($seed);

            usort($array, function () {
                return rand(-1, 1);
            });
        }

        return $array;
    }

    /**
     * 将数组转换为查询字符串。
     *
     * @param array $array
     * @return string
     */
    public static function query(array $array): string
    {
        return http_build_query($array, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * 使用给定的回调过滤数组。
     *
     * @param array $array
     * @param callable $callback
     * @return array
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 判断给定的 array 是否为 list
     * @param array $array
     * @return bool
     * @date 2025/7/23 上午11:19
     * @author 原点 467490186@qq.com
     */
    public static function isList(array $array): bool
    {
        return array_is_list($array);
    }
}
