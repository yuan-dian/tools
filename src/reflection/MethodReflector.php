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

use ReflectionMethod;
class MethodReflector
{
    use AttributesTrait;

    public function __construct(
        private readonly ReflectionMethod $reflectionMethod,
    ) {}

    public function getReflection(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    public function __call(string $name, array $args = []): mixed
    {
        return $this->reflectionMethod->$name(...$args);
    }
}