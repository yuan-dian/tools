# yuandian/tools

高性能 PHP 工具包，提供字符串、数组、日期、验证、流式处理等常用功能。

## 安装

```bash
composer require yuandian/tools
```

## 功能模块

| 模块             | 方法数 | 说明               |
|----------------|-----|------------------|
| BeanUtil       | 6   | 实体转换（数组↔对象↔JSON） |
| StrUtil        | 65  | 字符串操作            |
| ArrUtil        | 95  | 数组操作             |
| DateUtil       | 62  | 日期时间操作           |
| ValidatorUtil  | 52  | 数据验证             |
| Stream         | 43  | 流式操作             |
| CollectionUtil | 45  | 集合操作             |
| RandomUtil     | 18  | 随机数生成            |
| ReUtil         | 19  | 正则操作             |
| ObjectUtil     | 12  | 对象操作             |
| NumberUtil     | 14  | 数字操作             |
| MapUtil        | 12  | Map操作            |
| IdUtil         | 6   | ID生成             |
| UUIDUtil       | 3   | UUID生成           |
| SnowflakeUtil  | 2   | 雪花算法             |
| DigestUtil     | 5   | 摘要算法             |
| CharsetUtil    | 4   | 字符集操作            |

## 使用示例

### BeanUtil - 实体转换

```php
use yuandian\Tools\bean\BeanUtil;

// 数组转对象
$user = BeanUtil::arrayToObject($userData, User::class);

// 对象转数组
$array = BeanUtil::objectToArray($user);

// 对象转对象
$vo = BeanUtil::objectToObject($user, UserVO::class);

// JSON转对象
$user = BeanUtil::jsonToObject($jsonString, User::class);
```

### StrUtil - 字符串操作

```php
use yuandian\Tools\utils\StrUtil;

// 判断
StrUtil::isEmpty('');           // true
StrUtil::isBlank('  ');         // true
StrUtil::contains('hello', 'll'); // true
StrUtil::startsWith('hello', 'he'); // true
StrUtil::endsWith('hello', 'lo');   // true

// 转换
StrUtil::snake('helloWorld');   // hello_world
StrUtil::camel('hello_world');  // helloWorld
StrUtil::studly('hello_world'); // HelloWorld
StrUtil::kebab('helloWorld');   // hello-world
StrUtil::swapCase('Hello');     // hELLO

// 操作
StrUtil::repeat('abc', 3);     // abcabcabc
StrUtil::reverse('hello');     // olleh
StrUtil::remove('hello world', ' world'); // hello
StrUtil::replace('hello', 'l', 'r');      // herro
StrUtil::abbreviate('这是一段很长的字符串', 6); // 这是一...
StrUtil::format('Hello {}', 'World');     // Hello World
```

### ArrUtil - 数组操作

```php
use yuandian\Tools\utils\ArrUtil;

// 判断
ArrUtil::isEmpty([]);           // true
ArrUtil::contains([1,2,3], 2); // true

// 转换
ArrUtil::flatten([[1,2],[3]]);  // [1,2,3]
ArrUtil::groupBy($users, 'age');
ArrUtil::keyBy($users, 'id');
ArrUtil::pluck($users, 'name');

// 操作
ArrUtil::take([1,2,3,4,5], 3); // [1,2,3]
ArrUtil::drop([1,2,3,4,5], 2); // [3,4,5]
ArrUtil::zip([1,2], ['a','b']); // [[1,'a'],[2,'b']]
ArrUtil::partition([1,2,3,4], fn($v) => $v%2===0);

// 统计
ArrUtil::min([1,2,3]);         // 1
ArrUtil::max([1,2,3]);         // 3
ArrUtil::sum([1,2,3]);         // 6
ArrUtil::frequency(['a','b','a']); // ['a'=>2,'b'=>1]
```

### DateUtil - 日期操作

```php
use yuandian\Tools\utils\DateUtil;

// 创建
DateUtil::now();                            // 当前时间
DateUtil::parse('2024-06-15', 'Y-m-d');     // 解析日期
DateUtil::parseInt(1718409600);              // 时间戳转日期
DateUtil::range(1, 10);                     // 数字范围

// 加减
DateUtil::addDays($date, 5);                // 加5天
DateUtil::addHours($date, 2);               // 加2小时
DateUtil::addMonths($date, 1);              // 加1月

// 比较
DateUtil::isBefore($date1, $date2);         // 在之前
DateUtil::isAfter($date1, $date2);          // 在之后
DateUtil::isBetween($date, $start, $end);   // 在之间
DateUtil::isSameDay($date1, $date2);        // 同一天

// 判断
DateUtil::isToday($date);                   // 今天
DateUtil::isWeekend($date);                 // 周末
DateUtil::isFuture($date);                  // 未来

// 格式化
DateUtil::format($date, 'Y-m-d');           // 格式化
DateUtil::formatRelative($date);            // 相对时间（3分钟前）
DateUtil::formatChinese($date);             // 中文格式

// 边界
DateUtil::beginOfDay($date);                // 当天开始
DateUtil::endOfDay($date);                  // 当天结束
DateUtil::beginOfWeek($date);               // 本周开始
DateUtil::beginOfMonth($date);              // 月初
DateUtil::beginOfYear($date);               // 年初
```

### ValidatorUtil - 数据验证

```php
use yuandian\Tools\utils\ValidatorUtil;

// 基础验证
ValidatorUtil::isEmail('test@example.com');     // true
ValidatorUtil::isMobile('13800138000');         // true
ValidatorUtil::isUrl('https://example.com');    // true
ValidatorUtil::isIP('192.168.1.1');             // true
ValidatorUtil::isUUID('550e8400-e29b-41d4...'); // true

// 中国验证
ValidatorUtil::isIdCard('110101199001011234');  // 身份证
ValidatorUtil::isCarLicense('京A12345');        // 车牌号
ValidatorUtil::isCreditCode('91110105...');     // 统一信用代码

// 类型验证
ValidatorUtil::isInteger('123');                // 整数
ValidatorUtil::isFloat('12.3');                 // 浮点数
ValidatorUtil::isJson('{"a":1}');               // JSON
ValidatorUtil::isDate('2024-06-15');            // 日期
ValidatorUtil::isHexColor('#ff00ff');           // 十六进制颜色

// 范围验证
ValidatorUtil::isBetween(5, 1, 10);            // 在范围内
ValidatorUtil::isLengthBetween('hi', 3, 10);   // 长度在范围内
ValidatorUtil::isStrongPassword('Abc123!@');    // 强密码

// 校验抛异常
ValidatorUtil::validateEmail('invalid');        // 抛异常
ValidatorUtil::notEmpty(null);                  // 抛异常
```

### Stream - 流式操作

```php
use yuandian\Tools\utils\Stream;

// 创建
Stream::of([1, 2, 3, 4, 5]);
Stream::range(1, 100);
Stream::iterate(1, 1);  // 无限流

// 转换
Stream::of($array)
    ->filter(fn($n) => $n > 2)
    ->map(fn($n) => $n * 2)
    ->distinct()
    ->sorted()
    ->toArray();

// 截取
Stream::of($array)->take(10)->toArray();
Stream::of($array)->skip(5)->toArray();
Stream::of($array)->takeWhile(fn($n) => $n < 5)->toArray();

// 统计
Stream::of($array)->count();
Stream::of($array)->sum();
Stream::of($array)->min();
Stream::of($array)->max();
Stream::of($array)->average();

// 查找
Stream::of($array)->find(fn($n) => $n > 3);
Stream::of($array)->anyMatch(fn($n) => $n === 2);
Stream::of($array)->allMatch(fn($n) => $n > 0);

// 合并
Stream::concat($stream1, $stream2)->toArray();
```

### CollectionUtil - 集合操作

```php
use yuandian\Tools\utils\CollectionUtil;

// 基础操作
CollectionUtil::isEmpty($collection);
CollectionUtil::contains($collection, $element);
CollectionUtil::size($collection);

// 集合运算
CollectionUtil::intersection($arr1, $arr2);  // 交集
CollectionUtil::union($arr1, $arr2);         // 并集
CollectionUtil::subtract($arr1, $arr2);      // 差集

// 分组
CollectionUtil::groupBy($users, 'age');
CollectionUtil::partition($array, fn($v) => $v > 0);

// 分页
CollectionUtil::paginate($array, 1, 10);
CollectionUtil::paginateWithInfo($array, 1, 10);

// 统计
CollectionUtil::stats($array);  // min, max, avg, sum, count
```

## 目录结构

```
src/
├── bean/               # 实体转换
│   ├── BeanUtil.php
│   └── mapper/
├── utils/              # 工具类
│   ├── StrUtil.php
│   ├── ArrUtil.php
│   ├── DateUtil.php
│   ├── ValidatorUtil.php
│   ├── Stream.php
│   ├── CollectionUtil.php
│   └── ...
├── reflection/         # 反射
├── feign/              # Feign客户端
├── http/               # HTTP客户端
├── enum/               # 枚举
├── attribute/          # 属性注解
└── const/              # 常量
```

## 捐献

![](./wechat.png)
![](./alipay.png)
