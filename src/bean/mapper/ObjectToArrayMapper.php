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

namespace yuandian\Tools\bean\mapper;

use BackedEnum;
use UnitEnum;
use yuandian\Tools\attribute\Alias;
use yuandian\Tools\attribute\ArrayOf;
use yuandian\Tools\attribute\Skip;
use yuandian\Tools\reflection\ClassReflector;
use yuandian\Tools\reflection\PropertyReflection;

/**
 * 对象转数组
 */
class ObjectToArrayMapper
{
    /**
     * @param object $from
     * @return array
     * @throws \ReflectionException
     * @date 2025/7/18 下午5:03
     * @author 原点 467490186@qq.com
     */
    public function map(object $from): array
    {
        // 优先使用toArray方法
        if (method_exists($from, 'toArray')) {
            return $from->toArray();
        }

        $class = new ClassReflector($from);
        $arrayProperties = [];
        foreach ($class->getPublicProperties() as $property) {
            // 判断属性是否初始化
            if(!$property->isInitialized($from)) {
                continue;
            }
            if($property->hasAttribute(Skip::class)) {
                continue;
            }
            $fieldName = $this->resolvePropertyName($property);
            $propertyValue = $this->resolvePropertyValue($property, $from);
            $arrayProperties[$fieldName] = $this->convertValueToArray($propertyValue, $fieldName, $property);
        }
        return $arrayProperties;
    }

    private function resolvePropertyName(PropertyReflection $property): string
    {
        $alias = $property->getAttribute(Alias::class);

        if ($alias !== null) {
            return $alias->name;
        }

        return $property->getName();
    }

    /**
     * @param PropertyReflection $property
     * @param object $object
     * @return mixed
     * @throws \ReflectionException
     * @date 2025/7/18 下午5:03
     * @author 原点 467490186@qq.com
     */
    private function resolvePropertyValue(PropertyReflection $property, object $object): mixed
    {
        $propertyName = $property->getName();

        //  尝试通过getter方法获取值
        $propertyValue = $this->getValueViaGetter($object, $propertyName);
        // 尝试通过__get魔术方法
        if ($propertyValue === null && method_exists($object, '__get')) {
            $propertyValue = $object->__get($propertyName);
        }
        //  尝试直接访问属性
        if ($propertyValue === null) {
            $propertyValue = $property->getValue($object);
        }
        return $propertyValue;
    }

    /**
     * @param object $object
     * @param string $propertyName
     * @return mixed
     * @date 2025/7/18 下午5:03
     * @author 原点 467490186@qq.com
     */
    private function getValueViaGetter(object $object, string $propertyName): mixed
    {
        // 尝试 getFieldName()
        $getter = 'get' . ucfirst($propertyName);
        if (method_exists($object, $getter)) {
            return $object->$getter();
        }

        // 尝试 isFieldName() (适用于布尔值)
        $isSer = 'is' . ucfirst($propertyName);
        if (method_exists($object, $isSer)) {
            return $object->$isSer();
        }

        return null;
    }

    /**
     * @param mixed $value
     * @param string $fieldName
     * @param PropertyReflection $property
     * @return mixed
     * @throws \ReflectionException
     * @date 2025/7/18 下午5:18
     * @author 原点 467490186@qq.com
     */
    private function convertValueToArray(mixed $value, string $fieldName, PropertyReflection $property): mixed
    {
        if ($value === null) {
            return null;
        }
        // 处理枚举类型
        if ($value instanceof UnitEnum) {
            return $value instanceof BackedEnum ? $value->value : $value->name;
        }
        if (is_array($value) && $property->getAttribute(ArrayOf::class)) {
            return array_map(fn($item) => $this->map($item), $value);
        }
        // 处理对象
        if (is_object($value)) {
            return $this->map($value);
        }

        return $value;
    }
}