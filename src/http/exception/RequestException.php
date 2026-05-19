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

namespace yuandian\Tools\http\exception;

use yuandian\Tools\http\Response;

/**
 * 请求异常（携带响应对象，便于在 catch 中读取响应内容）
 */
class RequestException extends HttpClientException
{
    private ?Response $response;

    public function __construct(
        string $message,
        string $curlError = '',
        ?Response $response = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    /**
     * 获取状态码（无响应时返回 0）
     */
    public function getStatusCode(): int
    {
        return $this->response?->getStatusCode() ?? 0;
    }
}