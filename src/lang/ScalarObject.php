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

abstract class ScalarObject implements \Stringable, \JsonSerializable
{
    public function __construct(protected mixed $value)
    {
    }

    public static function valueOf(mixed $value): static
    {
        return new static($value);
    }

    /**
     * 获取原始值
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * 转换为字符串表示
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * 比较两个标量对象是否相等
     */
    public function equals(mixed $obj): bool
    {
        if ($obj instanceof self) {
            return $this->value === $obj->getValue();
        }
        return $this->value === $obj;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}