<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/6/27
// +----------------------------------------------------------------------
namespace yuandian\Tools\enum;

interface ICode
{
    /**
     * 获取枚举值
     * @return string|int
     * @date 2026/5/27 下午5:17
     * @author 原点 467490186@qq.com
     */
    public function getCode(): string|int;

    /**
     * 获取状态码提示信息
     * @return string
     * @date 2023/8/29 13:53
     * @author 原点 467490186@qq.com
     */
    public function getMessage(): string;

    /**
     * 获取自定义属性值
     * @param string $key
     * @return mixed
     * @date 2026/5/27 下午4:54
     * @author 原点 467490186@qq.com
     */
    public function getExtra(string $key): mixed;

    /**
     * 获取全部属性值
     * @return array
     * @date 2026/5/27 下午4:54
     * @author 原点 467490186@qq.com
     */
    public function getExtras(): array;

}