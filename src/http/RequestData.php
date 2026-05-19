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

/**
 * 请求数据构建器
 */
class RequestData
{
    // Body 类型常量
    public const BODY_NONE = 'none';
    public const BODY_JSON = 'json';
    public const BODY_FORM = 'form';
    public const BODY_MULTIPART = 'multipart';
    public const BODY_RAW = 'raw';
    public const BODY_XML = 'xml';

    private string $method = HttpMethod::GET;
    private string $url = '';
    private array $headers = [];
    private ?array $query = null;
    private mixed $body = null;
    private string $bodyType = self::BODY_NONE;
    private ?int $timeout = 30;
    private ?int $connectTimeout = 5;
    private bool $verify = true;
    private ?string $cert = null;
    private ?string $proxy = null;
    private bool $followRedirects = true;
    private int $maxRedirects = 5;
    private ?array $auth = null;
    private ?array $cookies = null;
    private ?string $cookieFile = null;
    private ?string $userAgent = null;
    private array $curlOptions = [];

    // ========================= 构建方法 =========================

    public function method(string $method): static
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function withHeaders(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function withHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withQuery(array $params): static
    {
        $this->query = array_merge($this->query ?? [], $params);
        return $this;
    }

    /**
     * JSON 请求体
     */
    public function withJson(mixed $data): static
    {
        $this->body = $data;
        $this->bodyType = self::BODY_JSON;
        if (!isset($this->headers[Header::CONTENT_TYPE])) {
            $this->headers[Header::CONTENT_TYPE] = ContentType::JSON_UTF8;
        }
        return $this;
    }

    /**
     * 表单请求体
     */
    public function withForm(array $data): static
    {
        $this->body = $data;
        $this->bodyType = self::BODY_FORM;
        if (!isset($this->headers[Header::CONTENT_TYPE])) {
            $this->headers[Header::CONTENT_TYPE] = ContentType::FORM;
        }
        return $this;
    }

    /**
     * Multipart 表单（文件上传）
     */
    public function withMultipart(array $data): static
    {
        $this->body = $data;
        $this->bodyType = self::BODY_MULTIPART;
        return $this;
    }

    /**
     * 原始字符串 body
     */
    public function withBody(string $body, string $contentType = ContentType::TEXT_UTF8): static
    {
        $this->body = $body;
        $this->bodyType = self::BODY_RAW;
        if (!isset($this->headers[Header::CONTENT_TYPE])) {
            $this->headers[Header::CONTENT_TYPE] = $contentType;
        }
        return $this;
    }

    /**
     * XML 请求体
     */
    public function withXml(string $xml): static
    {
        $this->body = $xml;
        $this->bodyType = self::BODY_XML;
        if (!isset($this->headers[Header::CONTENT_TYPE])) {
            $this->headers[Header::CONTENT_TYPE] = ContentType::XML_UTF8;
        }
        return $this;
    }

    public function withTimeout(?int $seconds): static
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function withConnectTimeout(?int $seconds): static
    {
        $this->connectTimeout = $seconds;
        return $this;
    }

    public function withVerify(bool $verify): static
    {
        $this->verify = $verify;
        return $this;
    }

    public function withCert(string $certPath): static
    {
        $this->cert = $certPath;
        return $this;
    }

    public function withProxy(string $proxy): static
    {
        $this->proxy = $proxy;
        return $this;
    }

    public function withFollowRedirects(bool $follow, int $maxRedirects = 5): static
    {
        $this->followRedirects = $follow;
        $this->maxRedirects = $maxRedirects;
        return $this;
    }

    /**
     * Basic 认证
     */
    public function withBasicAuth(string $user, string $pass): static
    {
        $this->auth = [$user, $pass, AuthType::CURL_BASIC];
        return $this;
    }

    /**
     * Digest 认证
     */
    public function withDigestAuth(string $user, string $pass): static
    {
        $this->auth = [$user, $pass, AuthType::CURL_DIGEST];
        return $this;
    }

    /**
     * Bearer Token
     */
    public function withBearerToken(string $token): static
    {
        $this->headers[Header::AUTHORIZATION] = AuthType::bearerHeaderValue($token);
        return $this;
    }

    /**
     * API Key（放 Header）
     */
    public function withApiKey(string $key, string $headerName = Header::X_API_KEY): static
    {
        $this->headers[$headerName] = $key;
        return $this;
    }

    public function withCookies(array $cookies): static
    {
        $this->cookies = array_merge($this->cookies ?? [], $cookies);
        return $this;
    }

    public function withCookieFile(string $filePath): static
    {
        $this->cookieFile = $filePath;
        return $this;
    }

    public function withUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function withCurlOption(int $option, mixed $value): static
    {
        $this->curlOptions[$option] = $value;
        return $this;
    }

    public function withCurlOptions(array $options): static
    {
        $this->curlOptions = array_merge($this->curlOptions, $options);
        return $this;
    }

    // ========================= 构建 curl 选项 =========================

    public function buildCurlOptions(): array
    {
        $url = $this->buildUrl();
        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $this->method,
            CURLOPT_HTTPHEADER     => $this->buildHeaderLines(),
        ];

        // 超时
        if ($this->timeout !== null) {
            $opts[CURLOPT_TIMEOUT] = $this->timeout;
        }
        if ($this->connectTimeout !== null) {
            $opts[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        }

        // SSL
        $opts[CURLOPT_SSL_VERIFYPEER] = $this->verify;
        if (!$this->verify) {
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        if ($this->cert !== null) {
            $opts[CURLOPT_SSLCERT] = $this->cert;
        }

        // 代理
        if ($this->proxy !== null) {
            $opts[CURLOPT_PROXY] = $this->proxy;
        }

        // 重定向
        $opts[CURLOPT_FOLLOWLOCATION] = $this->followRedirects;
        if ($this->followRedirects) {
            $opts[CURLOPT_MAXREDIRS] = $this->maxRedirects;
        }

        // 认证
        if ($this->auth !== null) {
            $opts[CURLOPT_USERPWD] = $this->auth[0] . ':' . $this->auth[1];
            $opts[CURLOPT_HTTPAUTH] = $this->auth[2];
        }

        // Cookies
        if ($this->cookies !== null) {
            $opts[CURLOPT_COOKIE] = http_build_query($this->cookies, '', '; ');
        }
        if ($this->cookieFile !== null) {
            $opts[CURLOPT_COOKIEFILE] = $this->cookieFile;
            $opts[CURLOPT_COOKIEJAR] = $this->cookieFile;
        }

        // User-Agent
        if ($this->userAgent !== null) {
            $opts[CURLOPT_USERAGENT] = $this->userAgent;
        }

        // Body
        $this->applyBody($opts);

        // 原生选项（优先级最高）
        foreach ($this->curlOptions as $key => $value) {
            $opts[$key] = $value;
        }

        return $opts;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    // ========================= 内部方法 =========================

    private function buildUrl(): string
    {
        if ($this->query === null || $this->query === []) {
            return $this->url;
        }
        $separator = str_contains($this->url, '?') ? '&' : '?';
        return $this->url . $separator . http_build_query($this->query);
    }

    private function buildHeaderLines(): array
    {
        $lines = [];
        foreach ($this->headers as $name => $value) {
            $lines[] = $name . ': ' . $value;
        }
        return $lines;
    }

    private function applyBody(array &$opts): void
    {
        if (!HttpMethod::allowsBody($this->method)) {
            return;
        }

        switch ($this->bodyType) {
            case self::BODY_JSON:
                $opts[CURLOPT_POSTFIELDS] = json_encode(
                    $this->body,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
                break;
            case self::BODY_FORM:
                $opts[CURLOPT_POSTFIELDS] = is_array($this->body)
                    ? http_build_query($this->body)
                    : $this->body;
                break;
            case self::BODY_MULTIPART:
                $opts[CURLOPT_POSTFIELDS] = $this->buildMultipartBody();
                break;
            case self::BODY_RAW:
            case self::BODY_XML:
                $opts[CURLOPT_POSTFIELDS] = $this->body;
                break;
        }
    }

    private function buildMultipartBody(): array
    {
        $data = [];
        foreach ($this->body as $key => $value) {
            if (is_string($value) && str_starts_with($value, '@') && file_exists(substr($value, 1))) {
                $filePath = substr($value, 1);
                $data[$key] = new \CURLFile(
                    $filePath,
                    mime_content_type($filePath) ?: ContentType::OCTET_STREAM,
                    basename($filePath)
                );
            } elseif ($value instanceof \CURLFile) {
                $data[$key] = $value;
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}