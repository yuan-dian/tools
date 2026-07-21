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

/**
 * Feign 降级工厂接口
 *
 * 相比 handleFallback，FallbackFactory 可以访问原始异常对象
 *
 * 使用示例：
 *
 * #[FeignClient(name: 'user-center-auth', path: '/auth/token')]
 * #[ResponseMapping(0)]
 * abstract class AuthTokenClient implements FeignFallbackFactory
 * {
 *     #[FeignRoute('/disableToken', 'POST')]
 *     abstract public function disableToken(
 *         #[RequestBody] DisabledTokenRO $disabledToken
 *     ): array;
 *
 *     public static function create(\Throwable $e): object
 *     {
 *         return new class($e) {
 *             public function __construct(private \Throwable $e) {}
 *
 *             public function disableToken(DisabledTokenRO $ro): array {
 *                 Log::error("disableToken 降级", ['reason' => $this->e->getMessage()]);
 *                 return [];
 *             }
 *         };
 *     }
 * }
 */
interface FeignFallbackFactory
{
    /**
     * 创建降级实例
     * @param \Throwable $e 原始异常
     * @return object 降级实例，方法调用返回降级值
     */
    public static function create(\Throwable $e): object;
}
