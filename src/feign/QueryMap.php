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
 * 查询参数映射注解
 * 将对象的所有属性展开为 query 参数
 *
 * 示例：
 * #[FeignRoute('/search')]
 * public function search(#[QueryMap] AppSearchRO $search): array
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryMap
{
}
