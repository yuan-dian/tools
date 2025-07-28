<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/28
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\reflection;

use ReflectionParameter;

class ParameterReflector
{
    use AttributesTrait;

    public function __construct(
        private readonly ReflectionParameter $reflectionParameter,
    ) {}

    public function getReflection(): ReflectionParameter
    {
        return $this->reflectionParameter;
    }

    public function __call(string $name, array $args = []): mixed
    {
        return $this->reflectionParameter->$name(...$args);
    }
}