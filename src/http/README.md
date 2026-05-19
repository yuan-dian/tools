## 使用示例

```php

use yuandian\Tools\http\HttpClient;
use yuandian\Tools\http\constant\{
    HttpMethod,
    ContentType,
    Header,
    StatusCode,
    AuthType,
    Option,
};

$client = HttpClient::create()
    ->withHeader(Header::ACCEPT, ContentType::JSON)
    ->withBearerToken('token-xxx')
    ->withTimeout(15)
    ->withVerify(true);

// ========================= GET =========================

$res = $client->get('https://api.example.com/users', [
    Option::QUERY => ['page' => 1, 'size' => 20],
    Option::HEADERS => [
        Header::ACCEPT_LANGUAGE => 'zh-CN',
        Header::X_REQUEST_ID    => bin2hex(random_bytes(8)),
    ],
]);

// ========================= POST JSON =========================

$res = $client->post('https://api.example.com/users', [
    Option::JSON => ['name' => 'Tom', 'age' => 25],
]);

// ========================= POST 表单 =========================

$res = $client->post('https://api.example.com/login', [
    Option::FORM => ['username' => 'admin', 'password' => '123'],
]);

// ========================= POST XML =========================

$res = $client->post('https://api.example.com/soap', [
    Option::XML => '<request><action>query</action></request>',
]);

// ========================= POST 原始 Body =========================

$res = $client->post('https://api.example.com/raw', [
    Option::BODY         => '{"key":"value"}',
    Option::CONTENT_TYPE => ContentType::JSON_UTF8,
]);

// ========================= 文件上传 =========================

$res = $client->post('https://api.example.com/upload', [
    Option::MULTIPART => [
        'name' => '头像',
        'file' => new \CURLFile(
            '/path/to/photo.jpg',
            ContentType::IMAGE_JPEG,
            'photo.jpg'
        ),
    ],
]);

// ========================= 带认证 =========================

$res = $client->post('https://api.example.com/users', [
    Option::JSON => ['name' => 'Tom'],
    Option::AUTH => ['admin', 'secret', AuthType::BASIC],
]);

// ========================= 跳过 SSL + 代理 =========================

$res = $client->get('https://api.example.com/users', [
    Option::VERIFY => false,
    Option::PROXY  => 'http://127.0.0.1:7890',
]);

// ========================= 自定义超时 =========================

$res = $client->post('https://api.example.com/upload', [
    Option::JSON            => ['data' => str_repeat('x', 100000)],
    Option::TIMEOUT         => 60,
    Option::CONNECT_TIMEOUT => 10,
]);

// ========================= 原生 curl 选项 =========================

$res = $client->get('https://api.example.com/users', [
    Option::CURL => [
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ],
]);

// ========================= 并发 =========================

$responses = HttpClient::pool([
    [HttpMethod::GET,  'https://api.example.com/users'],
    [HttpMethod::GET,  'https://api.example.com/orders'],
    [HttpMethod::POST, 'https://api.example.com/logs', [
        Option::JSON => ['event' => 'sync'],
        Option::HEADERS => [
            Header::CONTENT_TYPE => ContentType::JSON_UTF8,
        ],
    ]],
]);

foreach ($responses as $i => $res) {
    if ($res->getStatusCode() === StatusCode::OK) {
        echo "Request {$i}: OK\n";
    }
}

// ========================= 响应判断 =========================

if ($res->isOk()) {
    $data = $res->json();
}

if ($res->getStatusCode() === StatusCode::UNAUTHORIZED) {
    echo '需要重新登录';
}

if ($res->getStatusCode() === StatusCode::TOO_MANY_REQUESTS) {
    echo '请求过于频繁，请稍后重试';
}

if ($res->isRetryable()) {
    echo '服务暂时不可用，可重试';
}

if ($res->isJson()) {
    $data = $res->json();
}
```