# 工具包


# 安装

``` composer require yuandian/tools ```
# 功能列表
- 实体工具包
  - 数组转对象
  - 对象转数组
  - 对象转对象

# 使用示例

```php
$userArray = [
    'name' => 'John Doe',
    'age' => 30,
    'status' => 'published',
    'address' => [
        'street' => 'Main St',
        'city' => 'New York'
    ],
    'addressHistory' => [
        ['street' => 'First Ave', 'city' => 'Chicago'],
        ['street' => 'Second St', 'city' => 'Boston']
    ]
];

$User = \yuandian\Tools\bean\BeanUtil::arrayToObject($userArray,\yuandian\Tools\Tests\User::class);
```

## 捐献

![](./wechat.png)
![](./alipay.png)