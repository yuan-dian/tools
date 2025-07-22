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
use yuandian\Tools\bean\BeanUtil;
use yuandian\Tools\Tests\entity\Address;
use yuandian\Tools\Tests\entity\Status;
use yuandian\Tools\Tests\entity\User;
use yuandian\Tools\Tests\entity\UserV2;

require __DIR__ . '/../vendor/autoload.php';

$address = new Address();
$address->street = 'Main St';
$address->city = 'New York';

$user = new User();
$user->name = 'John Doe';
$user->age = 30;
$user->status = Status::DRAFT;
$user->address = $address;
$user->addressHistory = [$address,$address];

$User2 = BeanUtil::objectToObject($user, UserV2::class);
var_dump($User2);