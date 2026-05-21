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
use yuandian\Tools\http\constant\Option;

#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestParam
{
    public function __construct(public ?string $name = null, public string $bodyType = Option::QUERY)
    {
    }
}