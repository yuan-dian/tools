<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/18
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\reflection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use yuandian\Tools\attribute\Alias;

class PHPReflectionProperty
{
    use AttributesTrait;
    public function __construct(
        private readonly ReflectionProperty $reflectionProperty,
    ) {
    }

    public function getName(): string
    {
        return $this->reflectionProperty->getName();
    }
    public function getValue(object $object): mixed
    {
        return $this->reflectionProperty->getValue($object);
    }

    public function getType(): \ReflectionIntersectionType|\ReflectionNamedType|\ReflectionUnionType|null
    {
        return $this->reflectionProperty->getType();
    }

    public function setValue(object $object, mixed $value): void
    {
        $this->reflectionProperty->setValue($object, $value);
    }

    public function isInitialized(object $object): bool
    {
        return $this->reflectionProperty->isInitialized($object);
    }
    public function isPublic(): bool
    {
        return $this->reflectionProperty->isPublic();
    }

    public function isReadonly(): bool
    {
        return $this->reflectionProperty->isReadOnly();
    }

    public function getReflection(): ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionParameter
    {
        return $this->reflectionProperty;
    }

    public function resolvePropertyName(): string
    {
        $alias = $this->getAttribute(Alias::class);
        if ($alias === null) {
            return $this->reflectionProperty->getName();
        }
        return $alias->name;
    }

    public function __call(string $name, array $args = []): mixed
    {
        return $this->reflectionProperty->$name(...$args);
    }
}