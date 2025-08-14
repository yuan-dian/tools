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
 */
class BeanUtil
{
    /**
     * 数组转对象
     * @template T of object
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
     * @template ItemObject of object
     * @param array $from
     * @param class-string<ItemObject>|ItemObject $to
     * @return array<ItemObject>
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
     * @template T of object
     * @param object $from
     * @param class-string<T> $to
     * @return T
     * @throws \ReflectionException
     * @date 2025/7/21 上午11:14
     * @author 原点 467490186@qq.com
     */
    public static function objectToObject(object $from, string|object $to): object
    {
        return (new ObjectToObjectMapper())->map($from, $to);
    }

}