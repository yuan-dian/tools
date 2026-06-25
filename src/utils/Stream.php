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

/**
 * Stream操作类 - 优化版
 * 
 * 特性:
 * 1. 惰性求值: 只有在终止操作时才执行所有操作
 * 2. 缓存机制: 结果会被缓存，重复调用不会重新计算
 * 3. 内存优化: 使用生成器避免创建中间数组
 * 
 * 使用示例:
 * $result = Stream::of([1, 2, 3, 4, 5])
 *     ->filter(fn($n) => $n > 2)
 *     ->map(fn($n) => $n * 2)
 *     ->toArray();
 * // 结果: [6, 8, 10]
 */
class Stream
{
    private ?array $source = null;
    private ?\Closure $generatorClosure = null;
    private array $operations = [];
    private ?array $cachedResult = null;

    private function __construct() {}

    /**
     * 从数组创建Stream
     * @param array $array
     * @return self
     */
    public static function of(array $array): self
    {
        $stream = new self();
        $stream->source = $array;
        return $stream;
    }

    /**
     * 从生成器创建Stream (内存友好)
     * @param \Generator $generator
     * @return self
     */
    public static function fromGenerator(\Generator $generator): self
    {
        $stream = new self();
        $stream->generatorClosure = function () use ($generator) {
            return $generator;
        };
        return $stream;
    }

    /**
     * 创建空Stream
     * @return self
     */
    public static function empty(): self
    {
        return self::of([]);
    }

    /**
     * 创建包含指定元素的Stream
     * @param mixed ...$elements
     * @return self
     */
    public static function ofElements(mixed ...$elements): self
    {
        return self::of($elements);
    }

    /**
     * 创建数字范围Stream
     * @param int $start
     * @param int $end
     * @param int $step
     * @return self
     */
    public static function range(int $start, int $end, int $step = 1): self
    {
        $stream = new self();
        $stream->generatorClosure = function () use ($start, $end, $step) {
            if ($step > 0) {
                for ($i = $start; $i <= $end; $i += $step) {
                    yield $i;
                }
            } else {
                for ($i = $start; $i >= $end; $i += $step) {
                    yield $i;
                }
            }
        };
        return $stream;
    }

    /**
     * 无限流
     * @param callable $generator 生成函数，返回null表示结束
     * @return self
     */
    public static function generate(callable $generator): self
    {
        $stream = new self();
        $stream->generatorClosure = function () use ($generator) {
            while (true) {
                $value = $generator();
                if ($value === null) {
                    break;
                }
                yield $value;
            }
        };
        return $stream;
    }

    /**
     * 无限递增流
     * @param int $start
     * @param int $step
     * @return self
     */
    public static function iterate(int $start, int $step = 1): self
    {
        $stream = new self();
        $stream->generatorClosure = function () use ($start, $step) {
            $current = $start;
            while (true) {
                yield $current;
                $current += $step;
            }
        };
        return $stream;
    }

    /**
     * 过滤
     * @param callable $predicate 谓词函数
     * @return self
     */
    public function filter(callable $predicate): self
    {
        $this->operations[] = ['filter', $predicate];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 映射
     * @param callable $mapper 映射函数
     * @return self
     */
    public function map(callable $mapper): self
    {
        $this->operations[] = ['map', $mapper];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 扁平化映射
     * @param callable $mapper 映射函数，返回数组
     * @return self
     */
    public function flatMap(callable $mapper): self
    {
        $this->operations[] = ['flatMap', $mapper];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 去重
     * @return self
     */
    public function distinct(): self
    {
        $this->operations[] = ['distinct', null];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 排序
     * @param callable|null $comparator 比较函数
     * @return self
     */
    public function sorted(?callable $comparator = null): self
    {
        $this->operations[] = ['sorted', $comparator];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 限制元素数量
     * @param int $maxSize 最大数量
     * @return self
     */
    public function limit(int $maxSize): self
    {
        $this->operations[] = ['limit', $maxSize];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 跳过前N个元素
     * @param int $n 跳过的数量
     * @return self
     */
    public function skip(int $n): self
    {
        $this->operations[] = ['skip', $n];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 对每个元素执行操作 (副作用)
     * @param callable $action 操作函数
     * @return self
     */
    public function peek(callable $action): self
    {
        $this->operations[] = ['peek', $action];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 取前N个元素
     * @param int $n
     * @return self
     */
    public function take(int $n): self
    {
        return $this->limit($n);
    }

    /**
     * 跳过前N个元素
     * @param int $n
     * @return self
     */
    public function drop(int $n): self
    {
        return $this->skip($n);
    }

    /**
     * 满足条件时持续取元素，遇到不满足时停止
     * @param callable $predicate
     * @return self
     */
    public function takeWhile(callable $predicate): self
    {
        $this->operations[] = ['takeWhile', $predicate];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 跳过满足条件的元素，遇到不满足时停止跳过
     * @param callable $predicate
     * @return self
     */
    public function dropWhile(callable $predicate): self
    {
        $this->operations[] = ['dropWhile', $predicate];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 合并多个Stream
     * @param self ...$streams
     * @return self
     */
    public static function concat(self ...$streams): self
    {
        $stream = new self();
        $stream->generatorClosure = function () use ($streams) {
            foreach ($streams as $s) {
                foreach ($s->toArray() as $item) {
                    yield $item;
                }
            }
        };
        return $stream;
    }

    /**
     * 创建可能为null的Stream
     * @param mixed $value
     * @return self
     */
    public static function ofNullable(mixed $value): self
    {
        if ($value === null) {
            return self::empty();
        }
        return is_array($value) ? self::of($value) : self::of([$value]);
    }

    /**
     * 滑动窗口
     * @param int $size 窗口大小
     * @return self
     */
    public function sliding(int $size): self
    {
        $this->operations[] = ['sliding', $size];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 分块
     * @param int $size 块大小
     * @return self
     */
    public function chunk(int $size): self
    {
        $this->operations[] = ['chunk', $size];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 分组
     * @param callable $keySelector 键选择函数
     * @return self
     */
    public function groupByKey(callable $keySelector): self
    {
        $this->operations[] = ['groupByKey', $keySelector];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 分区
     * @param callable $predicate 谓词函数
     * @return self
     */
    public function partition(callable $predicate): self
    {
        $this->operations[] = ['partition', $predicate];
        $this->cachedResult = null;
        return $this;
    }

    /**
     * 归约
     * @param callable $accumulator 累加函数
     * @param mixed $initial 初始值
     * @return mixed
     */
    public function reduce(callable $accumulator, mixed $initial = null): mixed
    {
        $data = $this->toArray();
        return array_reduce($data, $accumulator, $initial);
    }

    /**
     * 收集为数组
     * @return array
     */
    public function toArray(): array
    {
        if ($this->cachedResult !== null) {
            return $this->cachedResult;
        }

        $this->cachedResult = $this->collect();
        return $this->cachedResult;
    }

    /**
     * 收集为列表 (确保索引从0开始)
     * @return array
     */
    public function toList(): array
    {
        return array_values($this->toArray());
    }

    /**
     * 收集为关联数组
     * @param callable $keySelector
     * @param callable|null $valueSelector
     * @return array
     */
    public function toAssoc(callable $keySelector, ?callable $valueSelector = null): array
    {
        $data = $this->toArray();
        $result = [];
        foreach ($data as $item) {
            $key = $keySelector($item);
            $value = $valueSelector ? $valueSelector($item) : $item;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * 执行每个元素
     * @param callable $action
     * @return void
     */
    public function forEach(callable $action): void
    {
        $data = $this->toArray();
        foreach ($data as $item) {
            $action($item);
        }
    }

    /**
     * 统计元素数量
     * @return int
     */
    public function count(): int
    {
        return count($this->toArray());
    }

    /**
     * 检查是否为空
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * 检查是否非空
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * 获取第一个元素
     * @param mixed $default 默认值
     * @return mixed
     */
    public function first(mixed $default = null): mixed
    {
        $data = $this->toArray();
        return empty($data) ? $default : reset($data);
    }

    /**
     * 获取最后一个元素
     * @param mixed $default 默认值
     * @return mixed
     */
    public function last(mixed $default = null): mixed
    {
        $data = $this->toArray();
        return empty($data) ? $default : end($data);
    }

    /**
     * 查找第一个满足条件的元素
     * @param callable $predicate
     * @param mixed $default 默认值
     * @return mixed
     */
    public function find(callable $predicate, mixed $default = null): mixed
    {
        $data = $this->toArray();
        foreach ($data as $item) {
            if ($predicate($item)) {
                return $item;
            }
        }
        return $default;
    }

    /**
     * 检查是否所有元素都满足条件
     * @param callable $predicate
     * @return bool
     */
    public function allMatch(callable $predicate): bool
    {
        $data = $this->toArray();
        foreach ($data as $item) {
            if (!$predicate($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查是否任一元素满足条件
     * @param callable $predicate
     * @return bool
     */
    public function anyMatch(callable $predicate): bool
    {
        $data = $this->toArray();
        foreach ($data as $item) {
            if ($predicate($item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查是否没有元素满足条件
     * @param callable $predicate
     * @return bool
     */
    public function noneMatch(callable $predicate): bool
    {
        return !$this->anyMatch($predicate);
    }

    /**
     * 最小值
     * @return mixed
     */
    public function min(): mixed
    {
        $data = $this->toArray();
        return empty($data) ? null : min($data);
    }

    /**
     * 最大值
     * @return mixed
     */
    public function max(): mixed
    {
        $data = $this->toArray();
        return empty($data) ? null : max($data);
    }

    /**
     * 求和
     * @return float|int
     */
    public function sum(): float|int
    {
        return array_sum($this->toArray());
    }

    /**
     * 平均值
     * @return float|null
     */
    public function average(): ?float
    {
        $data = $this->toArray();
        return empty($data) ? null : array_sum($data) / count($data);
    }

    /**
     * 收集并执行所有操作 (使用生成器优化内存)
     * @return array
     */
    private function collect(): array
    {
        $generator = $this->getGenerator();
        $result = [];
        foreach ($generator as $item) {
            $result[] = $item;
        }
        return $result;
    }

    /**
     * 获取执行所有操作后的生成器
     * @return \Generator
     */
    private function getGenerator(): \Generator
    {
        // 获取源数据生成器
        $source = $this->getSourceGenerator();

        // 应用所有操作
        return $this->applyOperations($source);
    }

    /**
     * 获取源数据生成器
     * @return \Generator
     */
    private function getSourceGenerator(): \Generator
    {
        if ($this->generatorClosure !== null) {
            $generator = ($this->generatorClosure)();
            if ($generator instanceof \Generator) {
                return $generator;
            }
        }

        if ($this->source !== null) {
            return $this->arrayToGenerator($this->source);
        }

        return $this->emptyGenerator();
    }

    /**
     * 数组转生成器
     * @param array $array
     * @return \Generator
     */
    private function arrayToGenerator(array $array): \Generator
    {
        foreach ($array as $item) {
            yield $item;
        }
    }

    /**
     * 空生成器
     * @return \Generator
     */
    private function emptyGenerator(): \Generator
    {
        yield from [];
    }

    /**
     * 应用所有操作到生成器
     * @param \Generator $generator
     * @return \Generator
     */
    private function applyOperations(\Generator $generator): \Generator
    {
        $current = $generator;
        $count = 0;
        $skipCount = 0;
        $limitReached = false;

        foreach ($this->operations as [$type, $param]) {
            $previous = $current;

            switch ($type) {
                case 'filter':
                    $current = $this->applyFilter($previous, $param);
                    break;
                case 'map':
                    $current = $this->applyMap($previous, $param);
                    break;
                case 'flatMap':
                    $current = $this->applyFlatMap($previous, $param);
                    break;
                case 'distinct':
                    $current = $this->applyDistinct($previous);
                    break;
                case 'sorted':
                    $current = $this->applySorted($previous, $param);
                    break;
                case 'limit':
                    $current = $this->applyLimit($previous, $param);
                    break;
                case 'skip':
                    $current = $this->applySkip($previous, $param);
                    break;
                case 'peek':
                    $current = $this->applyPeek($previous, $param);
                    break;
                case 'sliding':
                    $current = $this->applySliding($previous, $param);
                    break;
                case 'chunk':
                    $current = $this->applyChunk($previous, $param);
                    break;
                case 'takeWhile':
                    $current = $this->applyTakeWhile($previous, $param);
                    break;
                case 'dropWhile':
                    $current = $this->applyDropWhile($previous, $param);
                    break;
                case 'groupByKey':
                    return $this->applyGroupByKey($previous, $param);
                case 'partition':
                    return $this->applyPartition($previous, $param);
            }
        }

        return $current;
    }

    /**
     * 应用过滤操作
     */
    private function applyFilter(\Generator $generator, callable $predicate): \Generator
    {
        foreach ($generator as $item) {
            if ($predicate($item)) {
                yield $item;
            }
        }
    }

    /**
     * 应用映射操作
     */
    private function applyMap(\Generator $generator, callable $mapper): \Generator
    {
        foreach ($generator as $item) {
            yield $mapper($item);
        }
    }

    /**
     * 应用扁平化映射操作
     */
    private function applyFlatMap(\Generator $generator, callable $mapper): \Generator
    {
        foreach ($generator as $item) {
            $mapped = $mapper($item);
            if (is_array($mapped)) {
                foreach ($mapped as $m) {
                    yield $m;
                }
            } else {
                yield $mapped;
            }
        }
    }

    /**
     * 应用去重操作
     */
    private function applyDistinct(\Generator $generator): \Generator
    {
        $seen = [];
        foreach ($generator as $item) {
            $key = is_object($item) ? spl_object_id($item) : $item;
            if (!in_array($key, $seen, true)) {
                $seen[] = $key;
                yield $item;
            }
        }
    }

    /**
     * 应用排序操作
     */
    private function applySorted(\Generator $generator, ?callable $comparator): \Generator
    {
        // 排序需要收集所有数据
        $data = [];
        foreach ($generator as $item) {
            $data[] = $item;
        }

        if ($comparator) {
            usort($data, $comparator);
        } else {
            sort($data);
        }

        foreach ($data as $item) {
            yield $item;
        }
    }

    /**
     * 应用限制操作
     */
    private function applyLimit(\Generator $generator, int $maxSize): \Generator
    {
        $count = 0;
        foreach ($generator as $item) {
            if ($count >= $maxSize) {
                break;
            }
            yield $item;
            $count++;
        }
    }

    /**
     * 应用跳过操作
     */
    private function applySkip(\Generator $generator, int $n): \Generator
    {
        $count = 0;
        foreach ($generator as $item) {
            if ($count < $n) {
                $count++;
                continue;
            }
            yield $item;
        }
    }

    /**
     * 应用takeWhile操作
     */
    private function applyTakeWhile(\Generator $generator, callable $predicate): \Generator
    {
        foreach ($generator as $item) {
            if (!$predicate($item)) {
                break;
            }
            yield $item;
        }
    }

    /**
     * 应用dropWhile操作
     */
    private function applyDropWhile(\Generator $generator, callable $predicate): \Generator
    {
        $dropping = true;
        foreach ($generator as $item) {
            if ($dropping && $predicate($item)) {
                continue;
            }
            $dropping = false;
            yield $item;
        }
    }

    /**
     * 应用peek操作
     */
    private function applyPeek(\Generator $generator, callable $action): \Generator
    {
        foreach ($generator as $item) {
            $action($item);
            yield $item;
        }
    }

    /**
     * 应用滑动窗口操作
     */
    private function applySliding(\Generator $generator, int $size): \Generator
    {
        $buffer = [];
        foreach ($generator as $item) {
            $buffer[] = $item;
            if (count($buffer) === $size) {
                yield $buffer;
                array_shift($buffer);
            }
        }
    }

    /**
     * 应用分块操作
     */
    private function applyChunk(\Generator $generator, int $size): \Generator
    {
        $chunk = [];
        foreach ($generator as $item) {
            $chunk[] = $item;
            if (count($chunk) === $size) {
                yield $chunk;
                $chunk = [];
            }
        }
        if (!empty($chunk)) {
            yield $chunk;
        }
    }

    /**
     * 应用分组操作
     */
    private function applyGroupByKey(\Generator $generator, callable $keySelector): \Generator
    {
        $result = [];
        foreach ($generator as $item) {
            $key = $keySelector($item);
            $result[$key][] = $item;
        }
        return $this->arrayToGenerator([$result]);
    }

    /**
     * 应用分区操作
     */
    private function applyPartition(\Generator $generator, callable $predicate): \Generator
    {
        $matching = [];
        $nonMatching = [];
        foreach ($generator as $item) {
            if ($predicate($item)) {
                $matching[] = $item;
            } else {
                $nonMatching[] = $item;
            }
        }
        return $this->arrayToGenerator([[$matching, $nonMatching]]);
    }
}
