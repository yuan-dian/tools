<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2024/9/10
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\bean;

use yuandian\Tools\bean\mapper\ArrayToObjectMapper;
use yuandian\Tools\bean\mapper\ObjectToArrayMapper;
use yuandian\Tools\bean\mapper\ObjectToObjectMapper;

/**
 * Bean 工具类
 * @template T of object
 */
class BeanUtil
{
    /**
     * 按照Bean对象属性创建对应的Class对象
     * @param array|object $from
     * @param class-string<T> $to
     * @return T|T[]
     * @date 2026/5/18 上午11:05
     * @author 原点 467490186@qq.com
     */
    public static function copyProperties(array|object|string $from, string|object $to): object|array
    {
        if (is_object($from)) {
            return (new ObjectToObjectMapper())->map($from, $to);
        }
        if (array_is_list($from)) {
            $data = [];
            foreach ($from as $item) {
                $data[] = (new ArrayToObjectMapper())->map($item, $to);
            }
            return $data;
        }
        if (is_array($from)) {
            return (new ArrayToObjectMapper())->map($from, $to);
        }
        if (is_string($from)) {
            if (function_exists("json_validate") && !json_validate($from)) {
                throw new \Exception("json_validate failed");
            }
            return (new ArrayToObjectMapper())->map(json_decode($from, true), $to);
        }
    }

    /**
     * 数组转对象
     * @param array $from
     * @param class-string<T> $to
     * @return T
     * @throws \ReflectionException
     * @date 2025/7/21 上午11:13
     * @author 原点 467490186@qq.com
     */
    public static function arrayToObject(array $from, string|object $to): object
    {
        return (new ArrayToObjectMapper())->map($from, $to);
    }

    /**
     * 数组转对象列表
     * @param array $from
     * @param class-string<T>|T $to
     * @return T[]
     * @throws \ReflectionException
     * @date 2025/7/21 上午11:04
     * @author 原点 467490186@qq.com
     */
    public static function arrayToObjectList(array $from, string|object $to): array
    {
        $data = [];
        foreach ($from as $item) {
            $data[] = (new ArrayToObjectMapper())->map($item, $to);
        }
        return $data;
    }

    /**
     * 对象转数组
     * @param object $from
     * @return array
     * @throws \ReflectionException
     * @date 2025/7/21 上午11:14
     * @author 原点 467490186@qq.com
     */
    public static function objectToArray(object $from): array
    {
        return (new ObjectToArrayMapper())->map($from);
    }

    /**
     * 对象转对象
     * @param object $from
     * @param class-string<T>|T $to
     * @return T
     * @throws \ReflectionException
     * @date 2025/7/21 上午11:14
     * @author 原点 467490186@qq.com
     */
    public static function objectToObject(object $from, string|object $to): object
    {
        return (new ObjectToObjectMapper())->map($from, $to);
    }

    /**
     * json字符串转对象
     * @template T of object
     * @param string $from
     * @param class-string<T> $to
     * @return T
     * @throws \ReflectionException
     * @throws \Exception
     * @date 2025/7/21 上午11:14
     * @author 原点 467490186@qq.com
     */
    public static function jsonToObject(string $from, string|object $to): object
    {
        if (function_exists("json_validate") && !json_validate($from)) {
            throw new \Exception("json_validate failed");
        }
        return (new ArrayToObjectMapper())->map(json_decode($from, true), $to);
    }

}