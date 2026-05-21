<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/21
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\feign;

/**
 * Feign 业务异常 —— 保留远程服务返回的完整信息
 */
class FeignException extends \RuntimeException
{
    public function __construct(
        private readonly string $serviceName,
        private readonly string $method,
        private readonly int|string $remoteCode,
        private readonly string $remoteMessage,
        private readonly mixed $remoteData,
        private readonly string $rawResponse,
    ) {
        $msg = sprintf(
            '[%s.%s] 业务异常: code=%s, message=%s',
            $serviceName,
            $method,
            $remoteCode,
            $remoteMessage
        );
        parent::__construct($msg);
    }

    /** 远程返回的 code */
    public function getRemoteCode(): int
    {
        return $this->remoteCode;
    }

    /** 远程返回的 message */
    public function getRemoteMessage(): string
    {
        return $this->remoteMessage;
    }

    /** 远程返回的 data */
    public function getRemoteData(): mixed
    {
        return $this->remoteData;
    }

    /** 服务名 */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /** 调用的方法名 */
    public function getMethodName(): string
    {
        return $this->method;
    }

    /** 原始响应 JSON */
    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }


}