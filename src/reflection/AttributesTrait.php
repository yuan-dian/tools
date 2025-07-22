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

namespace yuandian\Tools\reflection;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

trait AttributesTrait
{
    abstract public function getReflection(): ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionParameter;

    /**
     * @param class-string $name
     */
    public function hasAttribute(string $name): bool
    {
        return $this->getReflection()->getAttributes($name) !== [];
    }

    /**
     * @template AttributeClass of object
     * @param class-string<AttributeClass> $attributeClass
     * @return AttributeClass|null
     */
    public function getAttribute(string $attributeClass): ?object
    {
        $attribute = $this->getReflection()->getAttributes(
            $attributeClass,
            ReflectionAttribute::IS_INSTANCEOF
        )[0] ?? null;
        return $attribute?->newInstance();
    }

    /**
     * @template AttributeClass of object
     * @param class-string<AttributeClass> $attributeClass
     * @return AttributeClass[]
     */
    public function getAttributes(string $attributeClass): array
    {
        return array_map(
            fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
            $this->getReflection()->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF),
        );
    }

}