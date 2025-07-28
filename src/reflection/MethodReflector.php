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

use ReflectionMethod;
use ReflectionParameter;

class MethodReflector
{
    use AttributesTrait;

    public function __construct(
        private readonly ReflectionMethod $reflectionMethod,
    ) {}

    public function getReflection(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    /**
     * @return ParameterReflector[]
     * @date 2025/7/28 下午2:03
     * @author 原点 467490186@qq.com
     */
    public function getParameters(): array
    {
        return array_map(
            fn(ReflectionParameter $property) => new ParameterReflector($property),
            $this->reflectionMethod->getParameters(),
        );
    }

    /**
     * @param string $name
     * @return ParameterReflector|null
     * @throws \ReflectionException
     * @date 2025/7/28 下午2:29
     * @author 原点 467490186@qq.com
     */
    public function getParameter(string $name): ?ParameterReflector
    {
        foreach ($this->getParameters() as $parameter) {
            if ($parameter->getName() === $name) {
                return $parameter;
            }
        }
        return null;
    }
    public function getNumberOfParameters(): int
    {
        return $this->reflectionMethod->getNumberOfParameters();
    }

    /**
     * @param object|null $object
     * @param array $args
     * @return mixed
     * @throws \ReflectionException
     * @date 2025/7/28 下午1:57
     * @author 原点 467490186@qq.com
     */
    public function invokeArgs(?object $object, array $args = []): mixed
    {
        return $this->reflectionMethod->invokeArgs($object, $args);
    }

    /**
     * @return ClassReflector
     * @throws \ReflectionException
     * @date 2025/7/28 下午1:57
     * @author 原点 467490186@qq.com
     */
    public function getDeclaringClass(): ClassReflector
    {
        return new ClassReflector($this->reflectionMethod->getDeclaringClass());
    }

    public function getName(): string
    {
        return $this->reflectionMethod->getName();
    }
    public function isStatic(): bool
    {
        return $this->reflectionMethod->isStatic();
    }

    public function __call(string $name, array $args = []): mixed
    {
        return $this->reflectionMethod->$name(...$args);
    }
}