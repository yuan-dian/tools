<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/11/4
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\lang;

class MultiStr extends Str
{
    public function length(): int
    {
        return mb_strlen($this->value);
    }

    public function indexOf(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        return mb_strpos($this->value, $needle, $offset, $encoding);
    }

    public function lastIndexOf(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        return mb_strrpos($this->value, $needle, $offset, $encoding);
    }

    public function pos(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        return mb_strpos($this->value, $needle, $offset, $encoding);
    }

    public function rpos(string $needle, int $offset = 0, ?string $encoding = null): false|int
    {
        return mb_strrpos($this->value, $needle, $offset, $encoding);
    }

    public function ipos(string $needle, int $offset = 0, ?string $encoding = null): int|false
    {
        return mb_stripos($this->value, $needle, $offset, $encoding);
    }

    /**
     * @see https://www.php.net/mb_substr
     */
    public function substr(int $start, ?int $length = null, ?string $encoding = null): static
    {
        return new static(mb_substr($this->value, $start, $length, $encoding));
    }

    /**
     * Given a multibyte string, return an array of its characters
     * @see https://www.php.net/mb_str_split
     * @param int $length
     * @return Arr
     * @date 2025/11/7 上午11:26
     * @author 原点 467490186@qq.com
     */
    public function chunk(int $length = 1): Arr
    {
        return static::detectArrayType(mb_str_split($this->value, $length));
    }
}