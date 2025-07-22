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

class User implements Loggable, Serializable
{
    #[Skip]
    public string $name;
    public int|string $age;
    public Status $status;
    public Address $address;

    #[ArrayOf(Address::class)]
    public array $addressHistory = [];

    public function log(): string {
        return "User: {$this->name}, {$this->age}";
    }

    public function serialize(): array {
        return ['name' => $this->name, 'age' => $this->age];
    }
}