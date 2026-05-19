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

use yuandian\Tools\http\constant\AuthType;
use yuandian\Tools\http\constant\ContentType;
use yuandian\Tools\http\constant\Header;
use yuandian\Tools\http\constant\HttpMethod;
use yuandian\Tools\http\constant\Option;
use yuandian\Tools\http\exception\RequestException;

/**
 * HTTP 客户端
 *
 * $options 键名全部使用 Option 常量：
 *
 * Option::HEADERS         => array   请求头
 * Option::QUERY           => array   查询参数
 * Option::JSON            => mixed   JSON 请求体
 * Option::FORM            => array   表单请求体
 * Option::MULTIPART       => array   Multipart 请求体
 * Option::BODY            => string  原始请求体
 * Option::XML             => string  XML 请求体
 * Option::CONTENT_TYPE    => string  原始 body 的 Content-Type
 * Option::TIMEOUT         => int     超时秒数
 * Option::CONNECT_TIMEOUT => int     连接超时秒数
 * Option::VERIFY          => bool    SSL 验证
 * Option::CERT            => string  证书路径
 * Option::PROXY           => string  代理地址
 * Option::AUTH            => array   [user, pass, type]
 * Option::CURL            => array   原生 curl 选项
 */
class HttpClient
{
    private const DEFAULT_USER_AGENT = 'YuanDian-HttpClient/1.0';

    private array $defaults = [
        'headers'        => [],
        'timeout'        => 30,
        'connectTimeout' => 5,
        'verify'         => true,
        'userAgent'      => self::DEFAULT_USER_AGENT,
    ];

    private MiddlewareStack $middlewareStack;

    public function __construct(array $defaults = [])
    {
        $this->defaults = array_merge($this->defaults, $defaults);
        $this->middlewareStack = new MiddlewareStack();
    }

    public static function create(array $defaults = []): static
    {
        return new static($defaults);
    }

    // ========================= Fluent 配置 =========================

    public function withHeaders(array $headers): static
    {
        $clone = clone $this;
        $clone->defaults['headers'] = array_merge($clone->defaults['headers'], $headers);
        return $clone;
    }

    public function withHeader(string $name, string $value): static
    {
        $clone = clone $this;
        $clone->defaults['headers'][$name] = $value;
        return $clone;
    }

    public function withTimeout(int $seconds): static
    {
        $clone = clone $this;
        $clone->defaults['timeout'] = $seconds;
        return $clone;
    }

    public function withConnectTimeout(int $seconds): static
    {
        $clone = clone $this;
        $clone->defaults['connectTimeout'] = $seconds;
        return $clone;
    }

    public function withVerify(bool $verify): static
    {
        $clone = clone $this;
        $clone->defaults['verify'] = $verify;
        return $clone;
    }

    public function withProxy(string $proxy): static
    {
        $clone = clone $this;
        $clone->defaults['proxy'] = $proxy;
        return $clone;
    }

    public function withUserAgent(string $ua): static
    {
        $clone = clone $this;
        $clone->defaults['userAgent'] = $ua;
        return $clone;
    }

    public function withBasicAuth(string $user, string $pass): static
    {
        $clone = clone $this;
        $clone->defaults['auth'] = [AuthType::BASIC, $user, $pass];
        return $clone;
    }

    public function withBearerToken(string $token): static
    {
        return $this->withHeader(Header::AUTHORIZATION, AuthType::bearerHeaderValue($token));
    }

    public function withMiddleware(Middleware|callable $middleware): static
    {
        $clone = clone $this;
        $clone->middlewareStack = clone $this->middlewareStack;
        $clone->middlewareStack->push($middleware);
        return $clone;
    }

    public function withCurlOption(int $option, mixed $value): static
    {
        $clone = clone $this;
        $clone->defaults['curlOptions'][$option] = $value;
        return $clone;
    }

    // ========================= HTTP 方法 =========================

    public function get(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::GET, $url, $options);
    }

    public function post(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::POST, $url, $options);
    }

    public function put(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::PUT, $url, $options);
    }

    public function patch(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::PATCH, $url, $options);
    }

    public function delete(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::DELETE, $url, $options);
    }

    public function head(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::HEAD, $url, $options);
    }

    public function options(string $url, array $options = []): Response
    {
        return $this->request(HttpMethod::OPTIONS, $url, $options);
    }

    // ========================= 核心请求 =========================

    /**
     * 发送请求
     *
     * @param string $method HttpMethod 常量
     * @param string $url 请求地址
     * @param array $options Option 常量作为键名
     */
    public function request(string $method, string $url, array $options = []): Response
    {
        $request = $this->buildRequest($method, $url, $options);

        return $this->middlewareStack->execute($request, function (RequestData $req): Response {
            return $this->executeCurl($req);
        });
    }

    // ========================= 并发 =========================

    /**
     * 并发请求
     *
     * $responses = HttpClient::pool([
     *     [HttpMethod::GET,  'https://api.example.com/a'],
     *     [HttpMethod::POST, 'https://api.example.com/b', [Option::JSON => [...]]],
     * ]);
     */
    public static function pool(array $requests, int $concurrency = 5): array
    {
        $multiHandle = curl_multi_init();
        $handles = [];

        foreach ($requests as $index => $req) {
            $method = $req[0] ?? HttpMethod::GET;
            $url = $req[1] ?? '';
            $opts = $req[2] ?? [];

            $client = new static();
            $request = $client->buildRequest($method, $url, $opts);
            $ch = curl_init();
            curl_setopt_array($ch, $request->buildCurlOptions());
            curl_multi_add_handle($multiHandle, $ch);
            $handles[$index] = $ch;
        }

        $running = 0;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);

        $responses = [];
        foreach ($handles as $index => $ch) {
            $responses[$index] = self::collectResponse($ch);
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);
        return $responses;
    }

    // ========================= 内部实现 =========================

    /**
     * 将 $options 构建为 RequestData
     */
    private function buildRequest(string $method, string $url, array $options): RequestData
    {
        $req = new RequestData();

        // 基础配置
        $req->method($method)
            ->url($url)
            ->withHeaders($this->defaults['headers'])
            ->withTimeout($options[Option::TIMEOUT] ?? $this->defaults['timeout'])
            ->withConnectTimeout($options[Option::CONNECT_TIMEOUT] ?? $this->defaults['connectTimeout'])
            ->withVerify($options[Option::VERIFY] ?? $this->defaults['verify'])
            ->withUserAgent($this->defaults['userAgent'] ?? self::DEFAULT_USER_AGENT);

        // 请求头
        if (isset($options[Option::HEADERS])) {
            $req->withHeaders($options[Option::HEADERS]);
        }

        // 查询参数
        if (isset($options[Option::QUERY])) {
            $req->withQuery($options[Option::QUERY]);
        }

        // Body —— 优先级: json > form > multipart > body > xml
        match (true) {
            isset($options[Option::JSON]) => $req->withJson($options[Option::JSON]),
            isset($options[Option::FORM]) => $req->withForm($options[Option::FORM]),
            isset($options[Option::MULTIPART]) => $req->withMultipart($options[Option::MULTIPART]),
            isset($options[Option::BODY]) => $req->withBody(
                $options[Option::BODY],
                $options[Option::CONTENT_TYPE] ?? ContentType::TEXT_UTF8
            ),
            isset($options[Option::XML]) => $req->withXml($options[Option::XML]),
            default => null,
        };

        // 认证
        if (isset($options[Option::AUTH])) {
            [$user, $pass, $type] = array_pad($options[Option::AUTH], 3, AuthType::BASIC);
            match ($type) {
                AuthType::DIGEST => $req->withDigestAuth($user, $pass),
                default => $req->withBasicAuth($user, $pass),
            };
        } elseif (isset($this->defaults['auth'])) {
            [$type, $user, $pass] = $this->defaults['auth'];
            match ($type) {
                AuthType::DIGEST => $req->withDigestAuth($user, $pass),
                default => $req->withBasicAuth($user, $pass),
            };
        }

        // 代理
        if (isset($options[Option::PROXY])) {
            $req->withProxy($options[Option::PROXY]);
        } elseif (isset($this->defaults['proxy'])) {
            $req->withProxy($this->defaults['proxy']);
        }

        // 证书
        if (isset($options[Option::CERT])) {
            $req->withCert($options[Option::CERT]);
        }

        // 原生 curl 选项
        if (isset($options[Option::CURL])) {
            $req->withCurlOptions($options[Option::CURL]);
        } elseif (isset($this->defaults['curlOptions'])) {
            $req->withCurlOptions($this->defaults['curlOptions']);
        }

        return $req;
    }

    private function executeCurl(RequestData $request): Response
    {
        $ch = curl_init();

        $responseHeaders = [];
        $curlOpts = $request->buildCurlOptions();
        $curlOpts[CURLOPT_HEADERFUNCTION] = function ($ch, $header) use (&$responseHeaders) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $responseHeaders[trim($parts[0])] = trim($parts[1]);
            }
            return strlen($header);
        };

        curl_setopt_array($ch, $curlOpts);

        $startTime = microtime(true);
        $body = curl_exec($ch);
        $elapsed = microtime(true) - $startTime;

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            throw new RequestException(
                "cURL error [{$request->getMethod()} {$request->getUrl()}]: {$error}",
                $error,
                new Response(0, '', [], $info, $error, $elapsed)
            );
        }

        $info = curl_getinfo($ch);
        curl_close($ch);

        return new Response(
            statusCode: $info['http_code'],
            body: $body ?: '',
            headers: $responseHeaders,
            info: $info,
            error: null,
            elapsed: $elapsed
        );
    }

    private static function collectResponse(\CurlHandle $ch): Response
    {
        $body = curl_multi_getcontent($ch);

        if (curl_errno($ch)) {
            return new Response(0, '', [], curl_getinfo($ch), curl_error($ch));
        }

        $info = curl_getinfo($ch);
        $headerSize = $info['header_size'] ?? 0;
        $headerStr = substr($body, 0, $headerSize);
        $headers = [];

        foreach (explode("\r\n", trim($headerStr)) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $headers[trim($parts[0])] = trim($parts[1]);
            }
        }

        return new Response(
            statusCode: $info['http_code'],
            body: substr($body, $headerSize),
            headers: $headers,
            info: $info,
        );
    }
}
