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

use yuandian\Tools\attribute\MapTo;

class Activity
{
    public string $type;

    // 方法1: 使用 MapTo 明确指定类型
    #[MapTo(User::class)]
    public Loggable&Serializable $entity;
}