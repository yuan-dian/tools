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

namespace yuandian\Tools\attribute;

use Attribute;

/**
 * 跳过属性
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Skip
{

}