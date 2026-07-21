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
 * 中间件栈
 * 洋葱模型执行
 */
class MiddlewareStack
{
    private array $middlewares = [];

    public function push(Middleware|callable $middleware): static
    {
        if ($middleware instanceof \Closure) {
            $middleware = new ClosureMiddleware($middleware);
        }
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * 执行中间件链，最终回调 $core 处理实际请求
     */
    public function execute(RequestData $request, callable $core): Response
    {
        $index = 0;
        $stack = $this;

        $next = function (RequestData $req) use (&$next, &$index, $stack, $core): Response {
            if ($index >= count($stack->middlewares)) {
                return $core($req);
            }
            $middleware = $stack->middlewares[$index++];
            return $middleware->handle($req, $next);
        };

        return $next($request);
    }

    /**
     * 执行前置中间件（用于 pool 场景）
     * 只执行中间件的前置逻辑，不执行 $next
     */
    public function executePreMiddleware(RequestData $request): RequestData
    {
        foreach ($this->middlewares as $middleware) {
            $noop = function (RequestData $req): Response {
                // 不执行实际请求，只返回 RequestData
                return new Response(0, '');
            };
            $middleware->handle($request, $noop);
        }
        return $request;
    }

    /**
     * 获取所有中间件
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function count(): int
    {
        return count($this->middlewares);
    }

    public function clear(): void
    {
        $this->middlewares = [];
    }
}