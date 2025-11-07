<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/11/5
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\lang;

class ArrayList extends ScalarObject implements \Serializable, \Countable
{
    public function __construct(array $value = [])
    {
        if (!array_is_list($value)) {
            throw new \InvalidArgumentException('ArrayList value must be list');
        }
        parent::__construct($value);
    }

    public function add(mixed $item): self
    {
        $this->value[] = $item;
        return $this;
    }


    public function get(int $index): mixed
    {
        if ($index < 0 || $index >= count($this->value)) {
            throw new \OutOfRangeException("Index out of bounds");
        }
        return $this->value[$index];
    }

    /**
     * Replaces the element at the specified position in this list with the specified element
     * @param int $index
     * @param mixed $item
     * @date 2024/6/7 上午11:46
     * @author 原点 467490186@qq.com
     */
    public function set(int $index, mixed $item): void
    {
        if ($index < 0 || $index >= count($this->value)) {
            throw new \OutOfRangeException("Index out of bounds");
        }
        $this->value[$index] = $item;
    }

    /**
     * Removes the element at the specified position in this list
     * @param int $index
     * @date 2024/6/7 上午11:46
     * @author 原点 467490186@qq.com
     */
    public function remove(int $index): void
    {
        if ($index < 0 || $index >= count($this->value)) {
            throw new \OutOfRangeException("Index out of bounds");
        }
        array_splice($this->value, $index, 1);
    }

    /**
     * Returns the number of elements in this list
     * @return int
     * @date 2024/6/7 上午11:46
     * @author 原点 467490186@qq.com
     */
    public function size(): int
    {
        return count($this->value);
    }

    /**
     * Returns true if this list contains no elements
     * @return bool
     * @date 2024/6/7 上午11:46
     * @author 原点 467490186@qq.com
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    /**
     * Removes all the elements from this list
     * @date 2024/6/7 上午11:47
     * @author 原点 467490186@qq.com
     */
    public function clear(): void
    {
        $this->value = [];
    }

    /**
     * Returns true if this list contains the specified element
     * @param mixed $item
     * @return bool
     * @date 2024/6/7 上午11:47
     * @author 原点 467490186@qq.com
     */
    public function contains(mixed $item): bool
    {
        return in_array($item, $this->value, true);
    }

    /**
     * Returns the index of the first occurrence of the specified element in this list, or -1 if this list does not contain the element
     * @param mixed $item
     * @return int
     * @date 2024/6/7 上午11:47
     * @author 原点 467490186@qq.com
     */
    public function indexOf(mixed $item): int
    {
        $index = array_search($item, $this->value, true);
        return $index !== false ? $index : -1;
    }

    public function count(): int
    {
        return count($this->value);
    }


    public function __toString(): string
    {
        return json_encode($this->value);
    }

    public function __toArray(): array
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }

    public function __serialize(): array
    {
        return $this->value;
    }

    public function __unserialize(array $data): void
    {
        $this->value = $data;
    }

    public function serialize(): string
    {
        return json_encode($this->value);
    }

    public function unserialize(string|\Stringable|Str $string): self
    {
        $this->value = (array)json_decode((string)$string, true);
        return $this;
    }
}