<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/21
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\bean\mapper;

/**
 * 对象转对象
 */
class ObjectToObjectMapper
{
    /**
     * @param object $from
     * @param string|object $to
     * @return object
     * @throws \ReflectionException
     * @date 2025/7/21 上午10:44
     * @author 原点 467490186@qq.com
     */
    public function map(object $from, string|object $to): object
    {
        $fromArray = (new ObjectToArrayMapper)->map($from);
        return (new ArrayToObjectMapper())->map($fromArray, $to);

    }

}