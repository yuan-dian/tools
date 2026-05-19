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

namespace yuandian\Tools\http\constant;

/**
 * 请求选项键名常量池
 *
 * 用于 HttpClient::request() 的 $options 参数以及各快捷方法
 */
final class Option
{
    // ========================= 请求头/查询 =========================
    public const HEADERS = 'headers';
    public const QUERY = 'query';

    // ========================= Body 类型 =========================
    public const JSON = 'json';
    public const FORM = 'form';
    public const MULTIPART = 'multipart';
    public const BODY = 'body';
    public const XML = 'xml';

    // ========================= Body 附加 =========================
    public const CONTENT_TYPE = 'content_type';

    // ========================= 超时 =========================
    public const TIMEOUT = 'timeout';
    public const CONNECT_TIMEOUT = 'connect_timeout';

    // ========================= SSL/代理 =========================
    public const VERIFY = 'verify';
    public const CERT = 'cert';
    public const PROXY = 'proxy';

    // ========================= 认证 =========================
    public const AUTH = 'auth';

    // ========================= curl 原生选项 =========================
    public const CURL = 'curl';

    private function __construct()
    {
    }
}