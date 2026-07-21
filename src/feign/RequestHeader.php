<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/7/21
// +----------------------------------------------------------------------

declare(strict_types=1);

namespace yuandian\Tools\feign;

use Attribute;

/**
 * 请求头注解
 * 用于注入自定义 HTTP Header
 *
 * 示例：
 * #[FeignRoute('/data')]
 * public function getData(
 *     #[RequestParam('id')] int $id,
 *     #[RequestHeader('Authorization')] string $token
 * ): DataVO
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestHeader
{
    public function __construct(
        public readonly ?string $name = null,
    ) {
    }
}
