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

use yuandian\Tools\attribute\MapTo;
use yuandian\Tools\reflection\ClassReflector;

/**
 * 实体基类
 * 用于自动生成getter|setter方法
 */
abstract class AbstractEntity
{
    private array $_builderAttributes = [];
    private static array $_propertyCache = [];


    /**
     * 动态设置器
     */
    public function __call(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'get')) {
            return $this->getProperty($method);
        }
        if (str_starts_with($method, 'set')) {
            return $this->setProperty($method, $args[0] ?? null);
        }
        throw new \InvalidArgumentException("undefined method {$method} does not exist in " . static::class);
    }

    /**
     * 获取属性值
     * @param string $method
     * @return mixed
     * @date 2025/10/16 上午10:05
     * @author 原点 467490186@qq.com
     */
    private function getProperty(string $method): mixed
    {
        $property = lcfirst(substr($method, 3));
        return $this->$property;
    }

    /**
     * 设置属性值
     * @param string $method
     * @param mixed $value
     * @return $this
     * @date 2025/10/16 上午10:05
     * @author 原点 467490186@qq.com
     */
    private function setProperty(string $method, mixed $value): static
    {
        $property = lcfirst(substr($method, 3));
        if ($this->propertyExists($property)) {
            $this->_builderAttributes[$property] = $value;
            return $this;
        }
        throw new \InvalidArgumentException("Property {$property} does not exist in " . static::class);
    }


    /**
     * 检查属性是否存在
     * @param string $property
     * @return bool
     * @date 2025/10/16 上午10:06
     * @author 原点 467490186@qq.com
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
     * @return array
     * @date 2025/10/16 上午10:06
     * @author 原点 467490186@qq.com
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

    public function __toString()
    {
        return json_encode($this);
    }


    /**
     * 开发工具：生成类注释
     */
    public static function generateClassDocBlock(): string
    {
        $reflection = new ClassReflector(static::class);
        $properties = $reflection->getPublicProperties();

        $lines = ["/**"];
        $getLines = [];
        $setLines = [];

        // 添加类描述
        $classComment = $reflection->getDocComment();
        if ($classComment) {
            $lines[] = " * " . trim(str_replace(['/**', '*/'], '', $classComment));
        }

        // 生成setter/getter方法注释
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $type = $property->getType();
            if ($mapToAttributes = $property->getAttribute(MapTo::class)) {
                $type = $mapToAttributes->className;
            }
            $type = explode('\\', (string)$type);
            $type = end($type);
            $setterName = 'set' . ucfirst($propertyName);
            $setLines[] = " * @method self $setterName($type \$$propertyName)";
            $getterName = 'get' . ucfirst($propertyName);
            $getLines[] = " * @method $type $getterName()";
        }
        $lines = array_merge($lines, $getLines);
        $lines = array_merge($lines, $setLines);
        $lines[] = " */";

        return implode("\n", $lines);
    }
}