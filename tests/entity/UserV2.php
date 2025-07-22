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

namespace yuandian\Tools\Tests\entity;

use yuandian\Tools\attribute\ArrayOf;
use yuandian\Tools\attribute\Skip;

class UserV2
{
    #[Skip]
    public string $name;
    public int $age;
    public Status $status;
    public Address $address;

    #[ArrayOf(Address::class)]
    public array $addressHistory = [];
}