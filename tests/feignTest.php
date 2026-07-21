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
use yuandian\Tools\feign\Feign;
use yuandian\Tools\Tests\entity\ShareInfoRO;
use yuandian\Tools\Tests\feign\AppClient;

require __DIR__ . '/../vendor/autoload.php';

Feign::registerService('uc-service', 'http://127.0.0.1:8788');

// ── 2. 创建客户端 ──
/** @var AppClient $appClient */
$appClient = Feign::create(AppClient::class);
$result = $appClient->detail("7fb3ad4b50e4fa40");

var_dump($result);