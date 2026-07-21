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

namespace yuandian\Tools\Tests\feign;

use yuandian\Tools\feign\FeignClient;
use yuandian\Tools\feign\FeignFallbackFactory;
use yuandian\Tools\feign\FeignRoute;
use yuandian\Tools\feign\RequestParam;
use yuandian\Tools\feign\ResponseMapping;
use yuandian\Tools\feign\ReturnType;
use yuandian\Tools\Tests\entity\ShareVO;

#[FeignClient(name: 'uc-service', path: '/share')]
#[ResponseMapping(successCode: 'server.success')]
abstract class AppClient implements FeignFallbackFactory
{
    #[FeignRoute('/info')]
    #[ReturnType(ShareVO::class)]
    abstract public function detail(#[RequestParam('shareCode')] string $appCode): array;

    public static function create(\Throwable $e): object
    {
        return new class($e) {
            public function __construct(private readonly \Throwable $e)
            {
            }

            public function detail(string $appCode): array
            {
                error_log("detail 降级 ['reason' =>" . $this->e->getMessage() . "]");
                return [];
            }
        };
    }
}