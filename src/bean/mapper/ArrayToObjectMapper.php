<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/17
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\bean\mapper;

use BackedEnum;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use UnitEnum;
use yuandian\Tools\attribute\ArrayOf;
use yuandian\Tools\attribute\MapTo;
use yuandian\Tools\attribute\Skip;
use yuandian\Tools\attribute\Trim;
use yuandian\Tools\lang\ScalarObject;
use yuandian\Tools\reflection\ClassReflector;
use yuandian\Tools\reflection\PropertyReflection;

/**
 * 数组转对象
 */
class ArrayToObjectMapper
{
    private const BUILTIN_TYPES = ['int' => true, 'float' => true, 'string' => true, 'bool' => true, 'array' => true];

    /**
     * @param array $from
     * @param string|object $to
     * @return object
     * @throws \ReflectionException
     * @date 2025/7/18 下午2:10
     * @author 原点 467490186@qq.com
     */
    public function map(array $from, string|object $to): object
    {
        $reflectionClass = new ClassReflector($to);
        $object = is_string($to) ? $reflectionClass->newInstanceWithoutConstructor() : $to;


        foreach ($reflectionClass->getPublicProperties() as $property) {
            if ($property->hasAttribute(Skip::class)) {
                continue;
            }
            $propertyName = $property->resolvePropertyName();

            if (!array_key_exists($propertyName, $from)) {
                continue;
            }
            $value = $this->resolveValue($property, $from[$propertyName], $property->getType());

            $property->setValue($object, $value);
        }
        return $object;
    }

    /**
     * @param PropertyReflection $property
     * @param mixed $value
     * @param ReflectionType|null $type
     * @return mixed
     * @throws \ReflectionException
     * @date 2025/7/18 下午2:40
     * @author 原点 467490186@qq.com
     */
    public function resolveValue(PropertyReflection $property, mixed $value, ?ReflectionType $type): mixed
    {
        // 处理null值
        if ($value === null && $type?->allowsNull()) {
            return null;
        }

        // 字符串预处理
        if (is_string($value)) {
            $value = $this->preprocessString($value, $property);
        }
        // 无类型声明时直接返回值
        if ($type === null) {
            return $value;
        }
        return match (true) {
            // 处理单一类型
            $type instanceof ReflectionNamedType => $this->handleNamedType($value, $type, $property),
            // 处理联合类型
            $type instanceof ReflectionUnionType => $this->handleUnionType($value, $type, $property),
            // 处理交叉类型
            $type instanceof ReflectionIntersectionType => $this->handleIntersectionType($value, $type, $property),
            default => $value
        };
    }

    /**
     * 字符串预处理
     * @param string $value
     * @param PropertyReflection $property
     * @return string
     * @date 2025/10/24 下午5:42
     * @author 原点 467490186@qq.com
     */
    private function preprocessString(string $value, PropertyReflection $property): string
    {
        $trim = $property->getAttribute(Trim::class);
        return $trim ? trim($value, $trim->characters) : $value;
    }

    /**
     * 处理单一类型
     * @param mixed $value
     * @param ReflectionNamedType $type
     * @param PropertyReflection $property
     * @return mixed
     * @throws \ReflectionException
     * @date 2025/7/18 下午2:35
     * @author 原点 467490186@qq.com
     */
    private function handleNamedType(
        mixed $value,
        ReflectionNamedType $type,
        PropertyReflection $property
    ): mixed {
        $typeName = $type->getName();
        // 处理对象数组
        if ($typeName === 'array') {
            return $this->handleArrayType($value, $property);
        }
        // 处理内置类型
        if (isset(self::BUILTIN_TYPES[$typeName])) {
            settype($value, $typeName);
            return $value;
        }
        // 处理枚举类型
        if (is_subclass_of($typeName, UnitEnum::class)) {
            return $this->createEnum($typeName, $value);
        }
        // 处理自定义标量对象
        if (is_subclass_of($typeName, ScalarObject::class)) {
            return new $typeName($value);
        }
        // 处理嵌套对象
        if (class_exists($typeName)) {
            return $this->map($value, $typeName);
        }
        return $value;
    }

    /**
     * 处理数组类型
     * @throws \ReflectionException
     */
    private function handleArrayType(mixed $value, PropertyReflection $property): array
    {
        $attribute = $property->getAttribute(ArrayOf::class);
        if (!$attribute) {
            return (array)$value;
        }

        return $this->createObjectArray((array)$value, $attribute->className);
    }

    /**
     * 创建对象列表
     * @param array $values
     * @param $className
     * @return array
     * @date 2025/7/18 下午1:59
     * @throws \ReflectionException
     * @author 原点 467490186@qq.com
     */
    private function createObjectArray(array $values, $className): array
    {
        return array_map(fn($item) => is_array($item) ? $this->map($item, $className) : $item,
            $values
        );
    }

    /**
     * 处理联合类型
     * @param mixed $value
     * @param ReflectionUnionType $type
     * @param PropertyReflection $property
     * @return mixed
     * @date 2025/7/18 下午2:35
     * @throws \ReflectionException
     * @author 原点 467490186@qq.com
     */
    private function handleUnionType(
        mixed $value,
        ReflectionUnionType $type,
        PropertyReflection $property
    ): mixed {
        $valueTypeName = self::getPhpTypeName($value);
        $types = $type->getTypes();
        // 优先匹配精确类型
        foreach ($types as $subType) {
            if ($subType instanceof ReflectionNamedType && $valueTypeName === $subType->getName()) {
                return $this->resolveValue($property, $value, $subType);
            }
        }

        // 类型转换尝试
        $errors = [];
        foreach ($types as $subType) {
            try {
                return $this->resolveValue($property, $value, $subType);
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
        throw new \RuntimeException(
            sprintf(
                'Union type conversion failed for value: %s. Errors: %s',
                json_encode($value),
                implode('; ', $errors)
            )
        );
    }

    /**
     * 获取PHP的原生类型名称
     *
     * @param $value
     * @return string
     * @date 2024/8/23 11:57
     * @author 原点 467490186@qq.com
     */
    private static function getPhpTypeName($value): string
    {
        $type = gettype($value);
        return match ($type) {
            'integer' => 'int',
            'double' => 'float',
            'boolean' => 'bool',
            'NULL' => 'null',
            default => $type,
        };
    }

    /**
     * 处理交叉类型
     * @param mixed $value
     * @param ReflectionIntersectionType $type
     * @param PropertyReflection $property
     * @return mixed
     * @throws \ReflectionException
     * @date 2025/7/18 下午3:02
     * @author 原点 467490186@qq.com
     */
    private function handleIntersectionType(
        mixed $value,
        ReflectionIntersectionType $type,
        PropertyReflection $property
    ): mixed {
        // 1. 检查是否指定了具体实现类
        $mapToAttributes = $property->getAttribute(MapTo::class);
        if (!$mapToAttributes) {
            throw new \RuntimeException(
                sprintf(
                    '% attribute, and no implementation class of the cross-type is specified',
                    $property->getName(),
                )
            );
        }
        foreach ($type->getTypes() as $subType) {
            if (!$subType instanceof ReflectionNamedType) {
                continue;
            }
            if (!is_subclass_of($mapToAttributes->className, $subType->getName())) {
                throw new \RuntimeException(
                    sprintf(
                        'The specified class: %s, does not satisfy the validation of the cross-type %s',
                        $mapToAttributes->className,
                        $subType->getName()
                    )
                );
            }
        }
        return $this->map($value, $mapToAttributes->className);
    }

    /**
     * 处理枚举类型的赋值
     *
     * @param mixed $value
     * @param string $enumClass
     * @return mixed
     */
    private static function createEnum(string $enumClass, mixed $value): mixed
    {
        // 如果值已经是枚举类型，直接赋值
        if ($value instanceof $enumClass) {
            return $value;
        }
        if (!is_string($value) && !is_int($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Enum value must be string or int, %s given for enum %s',
                    gettype($value),
                    $enumClass
                )
            );
        }

        // 处理基础枚举类型
        if (is_subclass_of($enumClass, BackedEnum::class)) {
            $result = $enumClass::tryFrom($value);
            if ($result !== null) {
                return $result;
            }
        }

        // 处理无值枚举
        if (is_subclass_of($enumClass, UnitEnum::class)) {
            if (is_string($value)) {
                $result = constant("{$enumClass}::{$value}");
                if ($result instanceof $enumClass) {
                    return $result;
                }
            }
        }
        throw new \InvalidArgumentException("Invalid enum value: $value");
    }

}