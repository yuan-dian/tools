<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/19
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\http;

/**
 * 闭包中间件适配器（允许直接传 callable）
 */
class ClosureMiddleware implements Middleware
{
    public function __construct(
        private readonly \Closure $closure
    ) {
    }

    public function handle(RequestData $request, callable $next): Response
    {
        return ($this->closure)($request, $next);
    }
}