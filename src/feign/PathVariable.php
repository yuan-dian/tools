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
 * 路径参数注解
 * 用于替换 URL 中的 {name} 占位符
 *
 * 示例：
 * #[FeignRoute('/user/{userId}')]
 * public function getUser(#[PathVariable('userId')] int $userId): UserVO
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class PathVariable
{
    public function __construct(
        public readonly ?string $name = null,
    ) {
    }
}
