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


class Long extends ScalarObject
{
    public function __construct(int|string $value)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Long value must be numeric');
        }
        parent::__construct((int)$value);
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function getValue(): int
    {
        return (int)$this->value;
    }

    public function jsonSerialize(): string
    {
        return (string)$this->value;
    }
}