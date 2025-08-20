<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/8/19
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;

use Throwable;

class HttpUtil
{
    private string $baseUri = '';
    private array $header = [];
    private bool $ssl = false;
    private bool $isPost = false;
    private string $router = '';
    // 请求get参数
    private array $query = [];
    // 请求post参数
    private array $body = [];
    // 超时时间
    private int $timeout = 3;

    private bool $isJson = true;

    public function __construct($config = [])
    {
        if (isset($config['base_uri'])) {
            $this->baseUri = $config['base_uri'];
        }
        if (isset($config['header'])) {
            $this->header = $config['header'];
        }
        if (isset($config['ssl'])) {
            $this->ssl = $config['ssl'];
        }
        if (isset($config['time_out'])) {
            $this->timeout = $config['time_out'];
        }
        if (isset($config['is_json'])) {
            $this->isJson = (bool)$config['is_json'];
        }
    }

    /**
     * get请求
     * @param string $router
     * @param array $query
     * @return string
     * @date 2020/5/20 15:31
     * @throws \Exception
     * @author 原点 467490186@qq.com
     */
    public function get(string $router, array $query): string
    {
        $this->router = $router;
        $this->query = $query;
        return $this->http_curl();
    }

    /**
     * post请求
     * @param string $router
     * @param array $body
     * @param array $query
     * @return string
     * @throws \Exception
     * @returnstring
     * @date 2022/11/28 15:54
     * @author 原点 467490186@qq.com
     */
    public function post(string $router, array $body = [], array $query = []): string
    {
        $this->isPost = true;
        $this->router = $router;
        $this->body = $body;
        $this->query = $query;
        return $this->http_curl();
    }


    /**
     * 发起请求
     * @return string
     * @date 2020/5/20 15:31
     * @throws \Exception
     * @author 原点 467490186@qq.com
     */
    private function http_curl(): string
    {
        try {
            $url = $this->baseUri . $this->router;
            if ($this->query) {
                $url .= '?' . http_build_query($this->query);
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($this->isPost) { //判断是否是POST请求
                curl_setopt($ch, CURLOPT_POST, 1);
                if ($this->isJson) {
                    // 使用json格式发送数据
                    $this->header = array_merge($this->header, ['Content-Type' => 'application/json;charset=utf-8']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->body));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->body));
                }
            }
            if ($this->header) {  //判断是否加header
                $headers = [];
                foreach ($this->header as $k => $v) {
                    $headers[] = $k . ':' . $v;
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            if (!$this->ssl) { //判断关闭开启ssl验证
                // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            // 设置超时时间
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

            $output = curl_exec($ch);
            curl_close($ch);

            if ($output === false) {
                throw new \Exception("请求失败");
            }
            //打印获得的数据
            return $output;
        } catch (Throwable $exception) {
            throw new \Exception("请求失败：" . $exception->getMessage());
        }
    }
}