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

use Attribute;

/**
 * 常量注解
 * Class Message
 * @package app\attribute
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class EnumMeat
{
    public string|array $extra;

    /**
     * $extra 为字符串时，默认赋值给message
     * @param string|array $extra
     */
    public function __construct(string|array $extra)
    {
        if (is_string($extra)) {
            $this->extra['message'] = $extra;
        } else {
            $this->extra = $extra;
        }
    }

    public function getMessage(): ?string
    {
        return $this->extra['message'] ?? null;
    }

    public function getExtra($key): mixed
    {
        return $this->extra[$key] ?? null;
    }

    public function getExtras(): array
    {
        return $this->extra ?? [];
    }
}