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

use yuandian\Tools\http\constant\ContentType;
use yuandian\Tools\http\constant\Header;
use yuandian\Tools\http\constant\StatusCode;

/**
 * HTTP 响应封装
 */
class Response
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $body,
        private readonly array $headers,
        private readonly array $info,
        private readonly ?string $error = null,
        private readonly float $elapsed = 0,
    ) {
    }

    // ========================= 状态码 =========================

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return StatusCode::getReasonPhrase($this->statusCode);
    }

    public function isOk(): bool
    {
        return StatusCode::isSuccess($this->statusCode);
    }

    public function isClientError(): bool
    {
        return StatusCode::isClientError($this->statusCode);
    }

    public function isServerError(): bool
    {
        return StatusCode::isServerError($this->statusCode);
    }

    public function isRedirect(): bool
    {
        return StatusCode::isRedirect($this->statusCode);
    }

    public function isRetryable(): bool
    {
        return StatusCode::isRetryable($this->statusCode);
    }

    public function requiresAuth(): bool
    {
        return StatusCode::requiresAuth($this->statusCode);
    }

    // ========================= 内容读取 =========================

    public function getBody(): string
    {
        return $this->body;
    }

    public function json(bool $associative = true): mixed
    {
        if ($this->body === '') {
            return null;
        }
        $result = json_decode($this->body, $associative);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('JSON decode error: ' . json_last_error_msg());
        }
        return $result;
    }

    public function xml(bool $asSimpleXml = true): mixed
    {
        if ($asSimpleXml) {
            $backup = libxml_use_internal_errors(true);
            $xml = simplexml_load_string($this->body);
            libxml_use_internal_errors($backup);
            if ($xml === false) {
                throw new \RuntimeException('XML parse error');
            }
            return $xml;
        }
        return $this->body;
    }

    public function jsonPath(string $path, mixed $default = null, string $separator = '.'): mixed
    {
        $data = $this->json();
        if (!is_array($data)) {
            return $default;
        }
        $keys = explode($separator, $path);
        $current = $data;
        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return $default;
            }
            $current = $current[$key];
        }
        return $current;
    }

    // ========================= Headers =========================

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 获取指定头（大小写不敏感）
     */
    public function getHeader(string $name): ?string
    {
        $lower = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $lower) {
                return $value;
            }
        }
        return null;
    }

    public function getContentType(): ?string
    {
        return $this->getHeader(Header::CONTENT_TYPE);
    }

    public function isJson(): bool
    {
        $ct = $this->getContentType();
        return $ct !== null && ContentType::isJson($ct);
    }

    public function isXml(): bool
    {
        $ct = $this->getContentType();
        return $ct !== null && ContentType::isXml($ct);
    }

    public function isBinary(): bool
    {
        $ct = $this->getContentType();
        return $ct !== null && ContentType::isBinary($ct);
    }

    // ========================= 元信息 =========================

    public function getInfo(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->info;
        }
        return $this->info[$key] ?? null;
    }

    public function getElapsed(): float
    {
        return $this->elapsed;
    }

    public function getEffectiveUrl(): ?string
    {
        return $this->info['url'] ?? null;
    }

    public function getContentLength(): int
    {
        return strlen($this->body);
    }

    // ========================= 错误 =========================

    public function getError(): ?string
    {
        return $this->error;
    }

    public function hasError(): bool
    {
        return $this->error !== null;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%d %s] %s (%.2fms)',
            $this->statusCode,
            $this->getReasonPhrase(),
            mb_substr($this->body, 0, 200),
            $this->elapsed * 1000
        );
    }
}