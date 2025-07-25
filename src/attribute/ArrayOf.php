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

namespace yuandian\Tools\attribute;

use Attribute;

/**
 * 指定数组元素类型
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayOf
{
    public function __construct(public string $className) {}
}