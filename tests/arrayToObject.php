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
use yuandian\Tools\Tests\entity\Activity;

require __DIR__ . '/../vendor/autoload.php';

$userArray = [
    'name' => 'John Doe',
    'age' => 30,
    'status' => 'published',
    'address' => [
        'street' => 'Main St',
        'city' => 'New York'
    ],
    'addressHistory' => [
        ['street' => 'First Ave', 'city' => 'Chicago'],
        ['street' => 'Second St', 'city' => 'Boston']
    ]
];

$User = \yuandian\Tools\bean\BeanUtil::arrayToObject($userArray, \yuandian\Tools\Tests\entity\User::class);
var_dump($User);


// 验证交叉类型
$activity = \yuandian\Tools\bean\BeanUtil::arrayToObject([
    'type' => 'login',
    'entity' => ['name' => 'John', 'age' => 30]
], Activity::class);
var_dump($activity);