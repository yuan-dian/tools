<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2024/6/7
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\lang;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class HashMap extends ScalarObject implements Countable, IteratorAggregate
{
    public function __construct(array $value = [])
    {
        if (array_is_list($value)) {
            throw new \InvalidArgumentException('HashMap value must be Map');
        }
        parent::__construct($value);
    }

    /**
     * Associates the specified value with the specified key in this map
     * @param mixed $key
     * @param mixed $value
     * @date 2024/6/7 上午11:42
     * @author 原点 467490186@qq.com
     */
    public function put(mixed $key, mixed $value): void
    {
        $this->value[$key] = $value;
    }

    /**
     * Returns the value to which the specified key is mapped, or null if this map contains no mapping for the key
     * @param mixed $key
     * @return mixed
     * @date 2024/6/7 上午11:43
     * @author 原点 467490186@qq.com
     */
    public function get(mixed $key): mixed
    {
        return $this->value[$key] ?? null;
    }

    /**
     * Removes the mapping for a key from this map if it is present
     * @param mixed $key
     * @date 2024/6/7 上午11:43
     * @author 原点 467490186@qq.com
     */
    public function remove(mixed $key): void
    {
        unset($this->value[$key]);
    }

    /**
     * Returns true if this map contains a mapping for the specified key
     * @param mixed $key
     * @return bool
     * @date 2024/6/7 上午11:43
     * @author 原点 467490186@qq.com
     */
    public function containsKey(mixed $key): bool
    {
        return array_key_exists($key, $this->value);
    }

    /**
     * Returns true if this map maps one or more keys to the specified value
     * @param mixed $value
     * @return bool
     * @date 2024/6/7 上午11:43
     * @author 原点 467490186@qq.com
     */
    public function containsValue(mixed $value): bool
    {
        return in_array($value, $this->value, true);
    }

    /**
     * Returns the number of key-value mappings in this map
     * @return int
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function size(): int
    {
        return count($this->value);
    }

    /**
     * Returns true if this map contains no key-value mappings
     * @return bool
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    /**
     * Removes all the mappings from this map
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function clear(): void
    {
        $this->value = [];
    }

    /**
     * Returns a Set view of the keys contained in this map
     * @return array
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function keySet(): array
    {
        return array_keys($this->value);
    }

    /**
     * Returns a Collection view of the values contained in this map
     * @return array
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function values(): array
    {
        return array_values($this->value);
    }

    /**
     * Returns a Set view of the mappings contained in this map
     * @return array
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function entrySet(): array
    {
        return $this->value;
    }

    /**
     * IteratorAggregate method
     * @return Traversable
     * @date 2024/6/7 上午11:44
     * @author 原点 467490186@qq.com
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->value);
    }

    /**
     * Count elements
     * @return int
     * @date 2024/6/7 上午11:45
     * @author 原点 467490186@qq.com
     */
    public function count(): int
    {
        return $this->size();
    }

    public function toArray(): array
    {
        return $this->value;
    }

    public function __toArray(): array
    {
        return $this->value;
    }
}