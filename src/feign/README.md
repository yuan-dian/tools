## 使用示例

```php

// ═══════════════════════════════════════════════════
//  interface：简单场景，异常直接抛出
// ═══════════════════════════════════════════════════
use yuandian\Tools\feign\FeignClient;use yuandian\Tools\feign\FeignRoute;use yuandian\Tools\feign\ResponseMapping;
#[FeignClient(name: 'app-service', path: '/app')]
#[ResponseMapping(0)]
interface AppClient
{
    #[FeignRoute('/detail')]
    public function detail(
        #[RequestParam('appCode')] string $appCode
    ): AppDetailVO;
    
    #[FeignRoute('/list')]
    #[ReturnType(AppDetailVO::class)]
    public function list(
        #[RequestParam('status')] int $status
    ): array;

    #[FeignRoute('/name')
    #[ResponseMapping(
        successCode: 200,
        codeName: 'status',
        messageName: 'msg',
        bodyName: 'result',
    )]
    public function getAppName(
        #[RequestParam('appCode')] string $appCode
    ): string;
}


// ═══════════════════════════════════════════════════
//  abstract class：需要细粒度控制异常
// ═══════════════════════════════════════════════════

#[FeignClient(name: 'pay-service', path: '/pay')]
#[ResponseMapping(200)]
abstract class PayClient implements FeignFallbackFactory
{
    #[FeignRoute('/balance')]
    abstract public function getBalance(
        #[RequestParam('userId')] int $userId
    ): float;

    #[FeignRoute('/create','POST')]
    abstract public function createOrder(
        #[RequestBody] OrderRO $order
    ): string;

   public static function create(\Throwable $e): object
    {
        return new class($e) {
            public function __construct(private readonly \Throwable $e)
            {
            }

            public function getBalance(int $userId): array
            {
                error_log("getBalance 降级 ['reason' =>" . $this->e->getMessage() . "]");
                return [];
            }
             public function createOrder(OrderRO $order): array
            {
                error_log("createOrder 降级 ['reason' =>" . $this->e->getMessage() . "]");
                return [];
            }
        };
    }
}

// 注册服务地址
Feign::registerService('app-service', 'https://api.example.com');
// 调用接口
$app = Feign::create(AppClient::class);
$detail = $app->detail('mall-app');
```