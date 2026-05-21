<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/21
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\feign;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ReturnType
{
    public function __construct(public readonly string $className)
    {
    }
}