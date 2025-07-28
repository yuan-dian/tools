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
use yuandian\Tools\reflection\ClassReflector;
use yuandian\Tools\reflection\PropertyReflection;

/**
 * 数组转对象
 */
class ArrayToObjectMapper
{
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
        if ($value === null && $type->allowsNull()) {
            return null;
        }

        if (is_string($value)) {
            $trim = $property->getAttribute(Trim::class);
            if ($trim) {
                $value = trim($value, $trim->characters);
            }
        }
        // 处理单一类型
        if ($type instanceof ReflectionNamedType) {
            return $this->handleNamedType($value, $type, $property);
        }
        // 处理联合类型
        if ($type instanceof ReflectionUnionType) {
            return $this->handleUnionType($value, $type, $property);
        }

        // 处理交叉类型（视为对象）
        if ($type instanceof ReflectionIntersectionType) {
            return $this->handleIntersectionType($value, $type, $property);
        }
        // 无类型声明时直接返回值
        return $value;
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
        if ($typeName === 'array' && $attribute = $property->getAttribute(ArrayOf::class)) {
            return $this->createObjectArray($value, $attribute->className);
        }
        // 处理内置类型
        if (in_array($typeName, ['int', 'float', 'string', 'bool', 'array'])) {
            settype($value, $typeName);
            return $value;
        }
        // 处理枚举类型
        if (is_subclass_of($typeName, UnitEnum::class)) {
            return $this->createEnum($typeName, $value);
        }

        // 处理嵌套对象
        if (class_exists($typeName)) {
            return $this->map($value, $typeName);
        }
        return $value;
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
            if ($valueTypeName === $subType->getName()) {
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
            if (!is_subclass_of($mapToAttributes->className,$subType->getName())) {
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
    private static function createEnum(
        string $enumClass,
        mixed $value,
    ): mixed {
        // 如果值已经是枚举类型，直接赋值
        if ($value instanceof $enumClass) {
            return $value;
        }
        // 处理基础枚举类型（BackedEnum）
        if (is_subclass_of($enumClass, BackedEnum::class)) {
            foreach ($enumClass::cases() as $case) {
                if ($case->value === $value) {
                    return $case;
                }
            }
        }
        // 处理无值枚举（UnitEnum）
        if (is_subclass_of($enumClass, UnitEnum::class)) {
            foreach ($enumClass::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }
        throw new \InvalidArgumentException("Invalid enum value: $value");
    }

}