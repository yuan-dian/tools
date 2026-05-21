## 使用示例

```php

// ═══════════════════════════════════════════════════
//  interface：简单场景，异常直接抛出，自动兜底处理的是网络异常
// ═══════════════════════════════════════════════════
use yuandian\Tools\feign\FeignClient;use yuandian\Tools\feign\FeignRoute;use yuandian\Tools\feign\ResponseCode;
#[FeignClient(name: 'app-service', path: '/app')]
#[ResponseCode(0)]
interface AppClient
{
    #[FeignRoute('/detail')]
    public function detail(
        #[RequestParam('appCode')] string $appCode
    ): AppDetailVO;

    #[FeignRoute('/name')]
    public function getAppName(
        #[RequestParam('appCode')] string $appCode
    ): string;
}


// ═══════════════════════════════════════════════════
//  abstract class：需要细粒度控制异常
// ═══════════════════════════════════════════════════

#[FeignClient(name: 'pay-service', path: '/pay')]
#[ResponseCode(200)]
abstract class PayClient implements \yuandian\Tools\feign\FeignFallback
{
    #[FeignRoute('/balance')]
    abstract public function getBalance(
        #[RequestParam('userId')] int $userId
    ): float;

    #[FeignRoute('/create','POST')]
    abstract public function createOrder(
        #[RequestBody] OrderRO $order
    ): string;

    /**
     * 区分两种异常：
     *   1. FeignException → 业务码不匹配（远程返回了，但业务失败）
     *   2. 其它 Throwable → 网络超时、连接拒绝等
     */
    protected static function handleFallback(string $method, array $args, \Throwable|FeignException $e): mixed 
    {
        // ── 业务异常：远程服务返回了，但 code 不对 ──
        if ($e instanceof FeignException) {
            error_log(sprintf(
                '[PayClient] %s 业务失败: code=%d msg=%s',
                $method,
                $e->getRemoteCode(),
                $e->getRemoteMessage(),
            ));

            return match ($method) {
                // 创建订单失败，返回空串
                'createOrder' => '',
                // 其它业务异常：不吞掉，原样抛出让上层处理
                default       => throw $e,
            };
        }

        // ── 网络异常：超时、连接拒绝等 ──
        error_log("[PayClient] {$method} 网络异常: {$e->getMessage()}");

        return match ($method) {
            'createOrder' => '',
            default       => null,  // 网络异常可以兜底
        };
    }
}

// 使用
$app = Feign::create(AppClient::class);
$detail = $app->detail('mall-app');
```