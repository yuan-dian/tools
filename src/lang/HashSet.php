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

class HashSet extends ScalarObject implements IteratorAggregate, Countable {

    /**
     * Adds the specified element to this set if it is not already present
     * @param mixed $item
     * @return bool
     * @date 2025/11/7 上午10:18
     * @author 原点 467490186@qq.com
     */
    public function add(mixed $item): bool {
        if (!$this->contains($item)) {
            $this->value[$this->hash($item)] = $item;
            return true;
        }
        return false;
    }

    /**
     * Removes the specified element from this set if it is present
     * @param mixed $item
     * @return bool
     * @date 2025/11/7 上午10:19
     * @author 原点 467490186@qq.com
     */
    public function remove(mixed $item): bool {
        $hash = $this->hash($item);
        if (isset($this->value[$hash])) {
            unset($this->value[$hash]);
            return true;
        }
        return false;
    }

    /**
     * Returns true if this set contains the specified element
     * @param mixed $item
     * @return bool
     * @date 2025/11/7 上午10:19
     * @author 原点 467490186@qq.com
     */
    public function contains(mixed $item): bool {
        return isset($this->value[$this->hash($item)]);
    }

    /**
     * Returns the number of elements in this set
     * @return int
     * @date 2025/11/7 上午10:19
     * @author 原点 467490186@qq.com
     */
    public function size(): int {
        return count($this->value);
    }

    /**
     * Returns true if this set contains no elements
     * @date 2025/11/7 上午10:19
     * @author 原点 467490186@qq.com
     */
    public function isEmpty(): bool {
        return empty($this->value);
    }

    /**
     * Removes all the elements from this set
     * @date 2025/11/7 上午10:19
     * @author 原点 467490186@qq.com
     */
    public function clear(): void {
        $this->value = [];
    }

    /**
     * Returns an iterator over the elements in this set
     * @date 2025/11/7 上午10:20
     * @author 原点 467490186@qq.com
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator(array_values($this->value));
    }

    /**
     * Returns a hash code value for the item
     * @param mixed $item
     * @return string
     * @date 2025/11/7 上午10:20
     * @author 原点 467490186@qq.com
     */
    private function hash(mixed $item): string {
        if (is_object($item)) {
            return spl_object_hash($item);
        } elseif (is_array($item)) {
            return md5(serialize($item));
        } else {
            return (string)$item;
        }
    }

    /**
     * Count elements
     * @return int
     * @date 2025/11/7 上午10:20
     * @author 原点 467490186@qq.com
     */
    public function count(): int {
        return $this->size();
    }
}