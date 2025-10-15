<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/10/15
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\entity;

use yuandian\Tools\reflection\ClassReflector;

abstract class AbstractEntity
{
    protected array $_builderAttributes = [];
    private static array $_propertyCache = [];


    /**
     * 动态设置器
     */
    public function __call(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'get')) {
            return $this->getProperty($method);
        }
        $property = $method;
        if (str_starts_with($method, 'set')) {
            $property = lcfirst(substr($method, 3));
        }
        if ($this->propertyExists($property)) {
            $this->_builderAttributes[$property] = $args[0] ?? null;
            return $this;
        }
        throw new \InvalidArgumentException("Property {$property} does not exist in " . static::class);
    }

    private function getProperty(string $method)
    {
        $property = lcfirst(substr($method, 3));
        return $this->$property;
    }


    /**
     * 检查属性是否存在
     */
    private function propertyExists(string $property): bool
    {
        $className = static::class;

        if (!isset(self::$_propertyCache[$className])) {
            self::$_propertyCache[$className] = $this->discoverProperties();
        }

        return in_array($property, self::$_propertyCache[$className]);
    }

    /**
     * 发现类的所有属性
     */
    private function discoverProperties(): array
    {
        $reflection = new ClassReflector($this);
        $properties = [];

        foreach ($reflection->getPublicProperties() as $property) {
            $properties[] = $property->getName();
        }

        return $properties;
    }

    /**
     * 应用构建的属性到对象
     */
    public function build(): self
    {
        foreach ($this->_builderAttributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        $this->_builderAttributes = [];
        return $this;
    }

    /**
     * 创建新实例并开始构建
     *
     */
    public static function builder(): self
    {
        return new static();
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return json_encode($this);
    }
}