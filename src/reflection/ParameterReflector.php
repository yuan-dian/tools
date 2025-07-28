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

    public function getName(): string
    {
        return $this->reflectionParameter->getName();
    }

    public function isOptional(): bool
    {
        return $this->reflectionParameter->isOptional();
    }

    public function isDefaultValueAvailable(): bool
    {
        return $this->reflectionParameter->isDefaultValueAvailable();
    }

    public function getPosition(): int
    {
        return $this->reflectionParameter->getPosition();
    }

    public function hasDefaultValue(): bool
    {
        return $this->reflectionParameter->isDefaultValueAvailable();
    }

    public function getDefaultValue(): mixed
    {
        return $this->reflectionParameter->getDefaultValue();
    }

    public function isVariadic(): bool
    {
        return $this->reflectionParameter->isVariadic();
    }

    /**
     * @throws \ReflectionException
     */
    public function getDeclaringClass(): ClassReflector
    {
        return new ClassReflector($this->reflectionParameter->getDeclaringClass());
    }

    public function __call(string $name, array $args = []): mixed
    {
        return $this->reflectionParameter->$name(...$args);
    }
}