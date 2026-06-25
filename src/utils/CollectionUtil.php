<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/6/25
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace yuandian\Tools\utils;

use InvalidArgumentException;

/**
 * 集合工具类
 * 提供类似Java CollectionUtils的功能
 */
class CollectionUtil
{

    /**
     * 判断集合是否为空 (null/空集合)
     * @param array|null $collection
     * @return bool
     */
    public static function isEmpty(?array $collection): bool
    {
        return $collection === null || count($collection) === 0;
    }

    /**
     * 判断集合是否非空
     * @param array|null $collection
     * @return bool
     */
    public static function isNotEmpty(?array $collection): bool
    {
        return !self::isEmpty($collection);
    }

    /**
     * 获取集合大小
     * @param array|null $collection
     * @return int
     */
    public static function size(?array $collection): int
    {
        return $collection === null ? 0 : count($collection);
    }

    /**
     * 安全获取集合元素
     * @param array $collection
     * @param int $index
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function get(array $collection, int $index, mixed $default = null): mixed
    {
        return $collection[$index] ?? $default;
    }

    /**
     * 添加元素到集合
     * @param array $collection
     * @param mixed $element
     * @return array
     */
    public static function add(array $collection, mixed $element): array
    {
        $collection[] = $element;
        return $collection;
    }

    /**
     * 添加多个元素到集合
     * @param array $collection
     * @param mixed ...$elements
     * @return array
     */
    public static function addAll(array $collection, mixed ...$elements): array
    {
        return array_merge($collection, $elements);
    }

    /**
     * 移除集合中指定元素 (只移除第一个匹配项)
     * @param array $collection
     * @param mixed $element
     * @return array
     */
    public static function remove(array $collection, mixed $element): array
    {
        $index = array_search($element, $collection, true);
        if ($index !== false) {
            array_splice($collection, $index, 1);
        }
        return $collection;
    }

    /**
     * 移除集合中指定索引的元素
     * @param array $collection
     * @param int $index
     * @return array
     */
    public static function removeAt(array $collection, int $index): array
    {
        if ($index < 0 || $index >= count($collection)) {
            throw new InvalidArgumentException("Index out of bounds: {$index}");
        }
        array_splice($collection, $index, 1);
        return $collection;
    }

    /**
     * 检查集合是否包含指定元素
     * @param array $collection
     * @param mixed $element
     * @return bool
     */
    public static function contains(array $collection, mixed $element): bool
    {
        return in_array($element, $collection, true);
    }

    /**
     * 检查集合是否包含所有指定元素
     * @param array $collection
     * @param array $elements
     * @return bool
     */
    public static function containsAll(array $collection, array $elements): bool
    {
        foreach ($elements as $element) {
            if (!in_array($element, $collection, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查集合是否包含任一指定元素
     * @param array $collection
     * @param array $elements
     * @return bool
     */
    public static function containsAny(array $collection, array $elements): bool
    {
        foreach ($elements as $element) {
            if (in_array($element, $collection, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取元素在集合中首次出现的索引 (找不到返回-1)
     * @param array $collection
     * @param mixed $element
     * @return int
     */
    public static function indexOf(array $collection, mixed $element): int
    {
        $index = array_search($element, $collection, true);
        return $index === false ? -1 : $index;
    }

    /**
     * 获取元素在集合中最后出现的索引 (找不到返回-1)
     * @param array $collection
     * @param mixed $element
     * @return int
     */
    public static function lastIndexOf(array $collection, mixed $element): int
    {
        for ($i = count($collection) - 1; $i >= 0; $i--) {
            if ($collection[$i] === $element) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * 获取集合的第一个元素
     * @param array $collection
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getFirst(array $collection, mixed $default = null): mixed
    {
        return empty($collection) ? $default : reset($collection);
    }

    /**
     * 获取集合的最后一个元素
     * @param array $collection
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getLast(array $collection, mixed $default = null): mixed
    {
        return empty($collection) ? $default : end($collection);
    }

    /**
     * 集合交集
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function intersection(array $array1, array $array2): array
    {
        return array_values(array_uintersect($array1, $array2, 'strcmp'));
    }

    /**
     * 集合并集
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function union(array $array1, array $array2): array
    {
        return array_values(array_unique(array_merge($array1, $array2)));
    }

    /**
     * 集合差集 (array1中有但array2中没有的元素)
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function subtract(array $array1, array $array2): array
    {
        return array_values(array_uintersect($array1, $array2, 'strcmp'));
    }

    /**
     * 集合补集 (array1中没有但array2中有的元素)
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function complement(array $array1, array $array2): array
    {
        return array_values(array_uintersect($array2, $array1, 'strcmp'));
    }

    /**
     * 对称差集 (只在其中一个集合中出现的元素)
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function symmetricDifference(array $array1, array $array2): array
    {
        return array_values(array_udiff(array_merge($array1, $array2), array_intersect($array1, $array2), 'strcmp'));
    }

    /**
     * 统计元素在集合中出现的次数
     * @param array $collection
     * @param mixed $element
     * @return int
     */
    public static function frequency(array $collection, mixed $element): int
    {
        return count(array_keys($collection, $element, true));
    }

    /**
     * 集合转置 (行转列)
     * @param array $array 二维数组
     * @return array
     */
    public static function transpose(array $array): array
    {
        $result = [];
        foreach ($array as $row => $cols) {
            foreach ($cols as $col => $value) {
                $result[$col][$row] = $value;
            }
        }
        return $result;
    }

    /**
     * 集合分组
     * @param array $collection
     * @param callable $keySelector
     * @return array
     */
    public static function groupBy(array $collection, callable $keySelector): array
    {
        $result = [];
        foreach ($collection as $item) {
            $key = $keySelector($item);
            $result[$key][] = $item;
        }
        return $result;
    }

    /**
     * 集合映射
     * @param array $collection
     * @param callable $mapper
     * @return array
     */
    public static function map(array $collection, callable $mapper): array
    {
        return array_map($mapper, $collection);
    }

    /**
     * 集合过滤
     * @param array $collection
     * @param callable $predicate
     * @return array
     */
    public static function filter(array $collection, callable $predicate): array
    {
        return array_values(array_filter($collection, $predicate));
    }

    /**
     * 集合归约
     * @param array $collection
     * @param callable $accumulator
     * @param mixed $initial 初始值
     * @return mixed
     */
    public static function reduce(array $collection, callable $accumulator, mixed $initial = null): mixed
    {
        return array_reduce($collection, $accumulator, $initial);
    }

    /**
     * 集合扁平化
     * @param array $array 多维数组
     * @param int $depth 展开深度 (1表示只展开一层)
     * @return array
     */
    public static function flatten(array $array, int $depth = 1): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                $result = array_merge($result, self::flatten($item, $depth - 1));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 集合分块
     * @param array $collection
     * @param int $size 每块大小
     * @return array
     */
    public static function chunk(array $collection, int $size): array
    {
        if ($size <= 0) {
            throw new InvalidArgumentException("Size must be positive");
        }
        return array_chunk($collection, $size);
    }

    /**
     * 集合去重
     * @param array $collection
     * @return array
     */
    public static function distinct(array $collection): array
    {
        return array_values(array_unique($collection));
    }

    /**
     * 集合反转
     * @param array $collection
     * @return array
     */
    public static function reverse(array $collection): array
    {
        return array_reverse($collection);
    }

    /**
     * 集合排序
     * @param array $collection
     * @param callable|null $comparator 比较函数
     * @return array
     */
    public static function sort(array $collection, ?callable $comparator = null): array
    {
        $sorted = $collection;
        if ($comparator) {
            usort($sorted, $comparator);
        } else {
            sort($sorted);
        }
        return $sorted;
    }

    /**
     * 集合填充
     * @param int $size
     * @param mixed $value
     * @return array
     */
    public static function fill(int $size, mixed $value): array
    {
        return array_fill(0, $size, $value);
    }

    /**
     * 集合裁剪
     * @param array $collection
     * @param int $start
     * @param int|null $length
     * @return array
     */
    public static function slice(array $collection, int $start, ?int $length = null): array
    {
        return array_slice($collection, $start, $length);
    }

    /**
     * 集合截断
     * @param array $collection
     * @param int $maxLength
     * @return array
     */
    public static function truncate(array $collection, int $maxLength): array
    {
        return array_slice($collection, 0, $maxLength);
    }

    /**
     * 集合连接
     * @param array $collection
     * @param string $separator
     * @return string
     */
    public static function join(array $collection, string $separator = ","): string
    {
        return implode($separator, $collection);
    }

    /**
     * 集合分页
     * @param array $collection
     * @param int $page 页码 (从1开始)
     * @param int $pageSize 每页大小
     * @return array
     */
    public static function paginate(array $collection, int $page, int $pageSize): array
    {
        $start = ($page - 1) * $pageSize;
        return array_slice($collection, $start, $pageSize);
    }

    /**
     * 集合分页信息
     * @param array $collection
     * @param int $page 页码 (从1开始)
     * @param int $pageSize 每页大小
     * @return array
     */
    public static function paginateWithInfo(array $collection, int $page, int $pageSize): array
    {
        $total = count($collection);
        $totalPages = (int)ceil($total / $pageSize);
        $page = max(1, min($page, $totalPages));
        $items = self::paginate($collection, $page, $pageSize);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1,
        ];
    }

    /**
     * 集合随机打乱
     * @param array $collection
     * @return array
     */
    public static function shuffle(array $collection): array
    {
        $shuffled = $collection;
        shuffle($shuffled);
        return $shuffled;
    }

    /**
     * 集合随机采样
     * @param array $collection
     * @param int $count 采样数量
     * @return array
     */
    public static function sample(array $collection, int $count): array
    {
        $count = min($count, count($collection));
        $keys = array_rand($collection, $count);
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $result = [];
        foreach ($keys as $key) {
            $result[] = $collection[$key];
        }
        return $result;
    }

    /**
     * 集合最小值
     * @param array $collection
     * @return mixed
     */
    public static function min(array $collection): mixed
    {
        if (self::isEmpty($collection)) {
            return null;
        }
        return min($collection);
    }

    /**
     * 集合最大值
     * @param array $collection
     * @return mixed
     */
    public static function max(array $collection): mixed
    {
        if (self::isEmpty($collection)) {
            return null;
        }
        return max($collection);
    }

    /**
     * 集合求和
     * @param array $collection
     * @return float|int
     */
    public static function sum(array $collection): float|int
    {
        return array_sum($collection);
    }

    /**
     * 集合平均值
     * @param array $collection
     * @return float|null
     */
    public static function average(array $collection): ?float
    {
        if (self::isEmpty($collection)) {
            return null;
        }
        return array_sum($collection) / count($collection);
    }

    /**
     * 集合统计 (最小值、最大值、平均值、求和、计数)
     * @param array $collection
     * @return array
     */
    public static function stats(array $collection): array
    {
        if (self::isEmpty($collection)) {
            return [
                'min' => null,
                'max' => null,
                'avg' => null,
                'sum' => 0,
                'count' => 0,
            ];
        }

        return [
            'min' => min($collection),
            'max' => max($collection),
            'avg' => array_sum($collection) / count($collection),
            'sum' => array_sum($collection),
            'count' => count($collection),
        ];
    }

    /**
     * 集合是否已排序 (升序)
     * @param array $collection
     * @return bool
     */
    public static function isSorted(array $collection): bool
    {
        $sorted = $collection;
        sort($sorted);
        return $sorted === $collection;
    }

    /**
     * 集合是否包含重复元素
     * @param array $collection
     * @return bool
     */
    public static function hasDuplicates(array $collection): bool
    {
        return count($collection) !== count(array_unique($collection));
    }

    /**
     * 集合是否为列表 (索引从0开始的连续数组)
     * @param array $collection
     * @return bool
     */
    public static function isList(array $collection): bool
    {
        return array_is_list($collection);
    }

    /**
     * 集合转换为键值对
     * @param array $collection
     * @param string $keyField
     * @param string $valueField
     * @return array
     */
    public static function toMap(array $collection, string $keyField, string $valueField): array
    {
        $result = [];
        foreach ($collection as $item) {
            if (is_array($item) && isset($item[$keyField], $item[$valueField])) {
                $result[$item[$keyField]] = $item[$valueField];
            }
        }
        return $result;
    }

    /**
     * 集合转换为关联数组
     * @param array $collection
     * @param callable $keySelector
     * @param callable|null $valueSelector
     * @return array
     */
    public static function toAssoc(array $collection, callable $keySelector, ?callable $valueSelector = null): array
    {
        $result = [];
        foreach ($collection as $item) {
            $key = $keySelector($item);
            $value = $valueSelector ? $valueSelector($item) : $item;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * 集合聚合
     * @param array $collection
     * @param callable $accumulator
     * @param mixed $initial
     * @return mixed
     */
    public static function aggregate(array $collection, callable $accumulator, mixed $initial = null): mixed
    {
        return array_reduce($collection, $accumulator, $initial);
    }

    /**
     * 集合分区 (根据谓词分为两组)
     * @param array $collection
     * @param callable $predicate
     * @return array [满足条件的, 不满足条件的]
     */
    public static function partition(array $collection, callable $predicate): array
    {
        $matching = [];
        $nonMatching = [];

        foreach ($collection as $item) {
            if ($predicate($item)) {
                $matching[] = $item;
            } else {
                $nonMatching[] = $item;
            }
        }

        return [$matching, $nonMatching];
    }

    /**
     * 集合压缩 (将多个数组合并为元组数组)
     * @param array ...$arrays
     * @return array
     */
    public static function zip(array ...$arrays): array
    {
        $result = [];
        $minLength = min(array_map('count', $arrays));

        for ($i = 0; $i < $minLength; $i++) {
            $tuple = [];
            foreach ($arrays as $array) {
                $tuple[] = $array[$i];
            }
            $result[] = $tuple;
        }

        return $result;
    }

    /**
     * 集合展开 (将元组数组展开为多个数组)
     * @param array $array 二维数组
     * @return array
     */
    public static function unzip(array $array): array
    {
        $result = [];
        foreach ($array as $tuple) {
            foreach ($tuple as $i => $value) {
                $result[$i][] = $value;
            }
        }
        return array_values($result);
    }

    /**
     * 集合滑动窗口
     * @param array $collection
     * @param int $size 窗口大小
     * @return array
     */
    public static function slidingWindow(array $collection, int $size): array
    {
        $result = [];
        $length = count($collection);

        for ($i = 0; $i <= $length - $size; $i++) {
            $result[] = array_slice($collection, $i, $size);
        }

        return $result;
    }

    /**
     * 集合累加
     * @param array $collection
     * @return array
     */
    public static function accumulate(array $collection): array
    {
        $result = [];
        $sum = 0;

        foreach ($collection as $item) {
            $sum += $item;
            $result[] = $sum;
        }

        return $result;
    }

    /**
     * 集合累加 (带初始值)
     * @param array $collection
     * @param mixed $initial
     * @param callable $accumulator
     * @return array
     */
    public static function accumulateWith(array $collection, mixed $initial, callable $accumulator): array
    {
        $result = [];
        $current = $initial;

        foreach ($collection as $item) {
            $current = $accumulator($current, $item);
            $result[] = $current;
        }

        return $result;
    }
}
