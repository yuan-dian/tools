<?php
// +----------------------------------------------------------------------
// | 数组转对象映射器
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/17
// +----------------------------------------------------------------------

declare(strict_types=1);

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
 * 数组转对象映射器
 */
class ArrayToObjectMapper
{
    /**
     * @param array $from 源数据
     * @param string|object $to 目标类名或对象实例
     * @return object
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public static function map(array $from, string|object $to): object
    {
        $reflector = new ClassReflector($to);
        $object = is_string($to) ? $reflector->newInstanceWithoutConstructor() : $to;

        foreach ($reflector->getPublicProperties() as $property) {
            if ($property->hasAttribute(Skip::class)) {
                continue;
            }

            $propertyName = $property->resolvePropertyName();

            if (!array_key_exists($propertyName, $from)) {
                continue;
            }

            $value = self::resolveValue($property, $from[$propertyName], $property->getType());
            $property->setValue($object, $value);
        }

        return $object;
    }

    /**
     * 解析并转换值
     *
     * @param PropertyReflection $property
     * @param mixed $value
     * @param ReflectionType|null $type
     * @return mixed
     * @throws \ReflectionException
     */
    public static function resolveValue(PropertyReflection $property, mixed $value, ?ReflectionType $type): mixed
    {
        // null 值处理
        if ($value === null && $type?->allowsNull()) {
            return null;
        }

        // 字符串预处理（Trim 等）
        if (is_string($value)) {
            $value = self::preprocessString($value, $property);
        }

        // 无类型声明时直接返回
        if ($type === null) {
            return $value;
        }

        return match (true) {
            $type instanceof ReflectionNamedType => self::handleNamedType($value, $type, $property),
            $type instanceof ReflectionUnionType => self::handleUnionType($value, $type, $property),
            $type instanceof ReflectionIntersectionType => self::handleIntersectionType($value, $type, $property),
            default => $value,
        };
    }

    /**
     * 字符串预处理
     */
    private static function preprocessString(string $value, PropertyReflection $property): string
    {
        $trim = $property->getAttribute(Trim::class);
        return $trim ? trim($value, $trim->characters) : $value;
    }

    /**
     * 处理单一命名类型
     *
     * @throws \ReflectionException
     */
    private static function handleNamedType(
        mixed $value,
        ReflectionNamedType $type,
        PropertyReflection $property
    ): mixed {
        $typeName = $type->getName();

        // array 类型
        if ($typeName === 'array') {
            return self::handleArrayType($value, $property);
        }

        // 内置标量类型
        if (isset(self::BUILTIN_TYPES[$typeName])) {
            return self::castBuiltin($value, $typeName);
        }

        // 枚举类型
        if (is_subclass_of($typeName, UnitEnum::class)) {
            return self::createEnum($typeName, $value);
        }

        // 嵌套对象
        if (class_exists($typeName)) {
            if (!is_array($value)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Cannot map %s to %s: expected array, %s given',
                        json_encode($value),
                        $typeName,
                        get_debug_type($value)
                    )
                );
            }
            return self::map($value, $typeName);
        }

        return $value;
    }

    /**
     * 处理 array 类型属性
     *
     * @throws \ReflectionException
     */
    private static function handleArrayType(mixed $value, PropertyReflection $property): array
    {
        if (!is_array($value)) {
            return (array)$value;
        }

        $attribute = $property->getAttribute(ArrayOf::class);
        if (!$attribute) {
            return $value;
        }

        return self::createObjectArray($value, $attribute->className);
    }

    /**
     * 将数组元素映射为对象列表
     *
     * @throws \ReflectionException
     */
    private static function createObjectArray(array $values, string $className): array
    {
        return array_map(
            static fn($item) => is_array($item) ? self::map($item, $className) : $item,
            $values
        );
    }

    /**
     * 处理联合类型 (A|B|C)
     *
     * 优先精确匹配，其次逐个尝试转换。
     *
     * @throws \ReflectionException
     */
    private static function handleUnionType(
        mixed $value,
        ReflectionUnionType $type,
        PropertyReflection $property
    ): mixed {
        $valueTypeName = self::getPhpTypeName($value);
        $types = $type->getTypes();

        // 1. 精确类型匹配
        foreach ($types as $subType) {
            if ($subType instanceof ReflectionNamedType && $valueTypeName === $subType->getName()) {
                return self::resolveValue($property, $value, $subType);
            }
        }

        // 2. 逐个尝试转换（仅捕获转换相关异常）
        $errors = [];
        foreach ($types as $subType) {
            try {
                return self::resolveValue($property, $value, $subType);
            } catch (\InvalidArgumentException|\RuntimeException $e) {
                $errors[] = $subType->getName() . ': ' . $e->getMessage();
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Union type resolution failed for value %s. Tried types [%s]. Errors: %s',
                json_encode($value),
                implode(', ', array_map(fn($t) => $t->getName(), $types)),
                implode(' | ', $errors)
            )
        );
    }

    /**
     * 处理交叉类型 (A&B)
     *
     * 必须通过 #[MapTo] 指定具体实现类。
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    private static function handleIntersectionType(
        mixed $value,
        ReflectionIntersectionType $type,
        PropertyReflection $property
    ): mixed {
        $mapTo = $property->getAttribute(MapTo::class);

        if (!$mapTo) {
            throw new \RuntimeException(
                sprintf(
                    'Property "%s" has intersection type and requires a #[MapTo] attribute to specify the implementation class',
                    $property->getName()
                )
            );
        }

        $targetClass = $mapTo->className;

        // 验证目标类满足交叉类型的所有接口/父类约束
        foreach ($type->getTypes() as $subType) {
            if (!$subType instanceof ReflectionNamedType) {
                continue;
            }
            if (!is_subclass_of($targetClass, $subType->getName()) && $targetClass !== $subType->getName()) {
                throw new \RuntimeException(
                    sprintf(
                        'Class "%s" does not satisfy intersection type constraint "%s"',
                        $targetClass,
                        $subType->getName()
                    )
                );
            }
        }

        return self::map($value, $targetClass);
    }

    // ========================================================================
    // 辅助方法
    // ========================================================================

    private const BUILTIN_TYPES = [
        'int'    => true,
        'float'  => true,
        'string' => true,
        'bool'   => true,
        'array'  => true,
    ];

    /**
     * 内置类型强转 —— 比 settype 语义更清晰、性能更好
     */
    private static function castBuiltin(mixed $value, string $type): int|float|string|bool|array
    {
        return match ($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'string' => (string)$value,
            'bool' => (bool)$value,
            'array' => (array)$value,
        };
    }

    /**
     * 获取 PHP 值的类型名（与反射类型名对齐）
     */
    private static function getPhpTypeName(mixed $value): string
    {
        return match (true) {
            is_int($value) => 'int',
            is_float($value) => 'float',
            is_bool($value) => 'bool',
            is_string($value) => 'string',
            is_array($value) => 'array',
            is_null($value) => 'null',
            is_object($value) => get_class($value),
            default => gettype($value),
        };
    }

    /**
     * 枚举解析 —— 支持 BackedEnum 和 UnitEnum
     *
     * @throws \InvalidArgumentException
     */
    private static function createEnum(string $enumClass, mixed $value): UnitEnum
    {
        // 已是目标枚举实例
        if ($value instanceof $enumClass) {
            return $value;
        }

        // BackedEnum: 用 tryFrom 精确匹配
        if (is_subclass_of($enumClass, BackedEnum::class)) {
            if (is_string($value) || is_int($value)) {
                $result = $enumClass::tryFrom($value);
                if ($result !== null) {
                    return $result;
                }
            }

            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid BackedEnum value for %s: %s (%s)',
                    $enumClass,
                    var_export($value, true),
                    get_debug_type($value)
                )
            );
        }
        // UnitEnum (无值枚举)
        if (is_subclass_of($enumClass, UnitEnum::class) && is_string($value)) {
            $cases = $enumClass::cases();
            foreach ($cases as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Invalid UnitEnum value for %s: %s (%s)',
                $enumClass,
                var_export($value, true),
                get_debug_type($value)
            )
        );
    }
}
