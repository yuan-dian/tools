## 使用示例

### 简单枚举

```php
<?php
use yuandian\Tools\enum\EnumMeat;
use yuandian\Tools\enum\EnumTrait;

enum PayType: int implements ICode
{
    use EnumTrait;

    #[EnumMeat('支付宝')]
    case ALIPAY = 1;

    #[EnumMeat('微信')]
    case WECHAT = 2;
    public function getCode(): int
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return $this->getExtra('message');
    }
}
```

### 多值枚举

```php
<?php
use yuandian\Tools\enum\ICode;
use yuandian\Tools\enum\EnumTrait;
use yuandian\Tools\enum\EnumTrait;

/**
 * @method string getChannel()
 */
enum PayType: int implements ICode
{
    use EnumTrait;

    #[EnumMeat(['message'=>'支付宝','channel' => 'alipay'])]
    case ALIPAY = 1;

    #[EnumMeat(['message'=>'微信','channel' => 'wechat'])]
    case WECHAT = 2;
    public function getCode(): int
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return $this->getExtra('message');
    }
}
```