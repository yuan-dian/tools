<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/20
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\feign;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class FeignRoute
{
    public function __construct(public string $path, public string $method = 'GET')
    {
    }
}