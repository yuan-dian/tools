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

namespace yuandian\Tools\reflection;

use ReflectionClass;
use ReflectionProperty;

class ClassReflector
{

    use AttributesTrait;

    // 缓存已反射的类，以避免重复创建
    private static array $reflectionCache = [];

    private readonly ReflectionClass $reflectionClass;


    /**
     * @param string|object $reflectionClass
     * @throws \ReflectionException
     */
    public function __construct(string|object $reflectionClass)
    {
        $this->reflectionClass = self::getReflectionClass($reflectionClass);
    }

    /**
     * 获取反射
     * @param string|object $object
     * @return ReflectionClass
     * @throws \ReflectionException
     * @date 2025/7/17 下午5:15
     * @author 原点 467490186@qq.com
     */
    public static function getReflectionClass(string|object $object): ReflectionClass
    {
        $className = is_object($object) ? get_class($object) : $object;

        if (!isset(self::$reflectionCache[$className])) {
            self::$reflectionCache[$className] = new ReflectionClass($className);
        }

        return self::$reflectionCache[$className];
    }

    /**
     * @throws \ReflectionException
     */
    public function newInstanceWithoutConstructor(): object
    {
        return $this->reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * @throws \ReflectionException
     */
    public function newInstanceArgs(array $args = []): object
    {
        return $this->reflectionClass->newInstanceArgs($args);
    }

    /**
     * Gets class name
     * @return string The class name.
     */
    public function getName(): string
    {
        return $this->reflectionClass->getName();
    }

    public function getShortName(): string
    {
        return $this->reflectionClass->getShortName();
    }

    public function isInterface(): bool
    {
        return $this->reflectionClass->isInterface();
    }

    public function isAbstract(): bool
    {
        return $this->reflectionClass->isAbstract();
    }

    /**
     * 获取对象公共属性
     * @return PropertyReflection[]
     * @date 2025/7/17 下午5:28
     * @author 原点 467490186@qq.com
     */
    public function getPublicProperties(): array
    {
        return array_map(
            fn(ReflectionProperty $property) => new PropertyReflection($property),
            $this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC),
        );
    }

    /**
     * 获取类属性的 ReflectionProperty
     * @param $name
     * @return PropertyReflection
     * @throws \ReflectionException
     * @date 2025/7/21 下午1:48
     * @author 原点 467490186@qq.com
     */
    public function getProperty($name): PropertyReflection
    {
        return new PropertyReflection($this->reflectionClass->getProperty($name));
    }

    /**
     * 检查是否定义了属性
     * @param $name
     * @return bool
     * @date 2025/7/25 下午5:38
     * @author 原点 467490186@qq.com
     */
    public function hasProperty($name): bool
    {
        return $this->reflectionClass->hasProperty($name);
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function __call(string $name, array $args = []): mixed
    {
        return $this->reflectionClass->$name(...$args);
    }
}