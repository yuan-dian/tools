<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/11/4
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\lang;

class Byte extends ScalarObject
{
    /**
     * A constant holding the minimum value a {@code byte} can
     * have, -2<sup>7</sup>.
     */
    const MIN_VALUE = -128;

    /**
     * A constant holding the maximum value a {@code byte} can
     * have, 2<sup>7</sup>-1.
     */
    const MAX_VALUE = 127;


    public function __construct(int $value)
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw new \InvalidArgumentException("Value out of range for byte: " . $value);
        }
        parent::__construct($value);
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}