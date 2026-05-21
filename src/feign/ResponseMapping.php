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

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ResponseMapping
{
    /**
     * @param int|string $successCode 成功错误码
     * @param string $codeName 错误码字段名称
     * @param string $messageName 消息字段名称
     * @param string $bodyName 业务数据字段名称
     */
    public function __construct(
        public readonly int|string $successCode = 0,
        public readonly string $codeName = 'code',
        public readonly string $messageName = 'message',
        public readonly string $bodyName = 'data',
    ) {
    }

}