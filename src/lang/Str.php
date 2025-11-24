<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/11/5
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\lang;

class Str extends ScalarObject
{

    public function __construct(string $value = '')
    {
        parent::__construct($value);
    }

    /**
     * Get string length
     * @return int
     * @date 2025/11/14 下午5:55
     * @author 原点 467490186@qq.com
     */
    public function length(): int
    {
        return strlen($this->value);
    }

    /**
     * Find the position of the first occurrence of a substring in a string
     * @param string $needle
     * @param int $offset
     * @return false|int
     * @date 2025/11/14 下午5:56
     * @author 原点 467490186@qq.com
     */
    public function indexOf(string $needle, int $offset = 0): false|int
    {
        return strpos($this->value, $needle, $offset);
    }

    /**
     * Find the position of the last occurrence of a substring in a string
     * @param string $needle
     * @param int $offset
     * @return false|int
     * @date 2025/11/14 下午5:57
     * @author 原点 467490186@qq.com
     */
    public function lastIndexOf(string $needle, int $offset = 0): false|int
    {
        return strrpos($this->value, $needle, $offset);
    }

    /**
     * Reverse a string
     * @return $this
     * @date 2025/11/14 下午5:58
     * @author 原点 467490186@qq.com
     */
    public function reverse(): static
    {
        return new static(strrev($this->value));
    }

    /**
     * Find position of first occurrence of a case-insensitive string
     * @return false|int
     */
    public function ipos(string $needle): bool|int
    {
        return stripos($this->value, $needle);
    }

    /**
     * Make a string lowercase
     * @return $this
     * @date 2025/11/14 下午6:00
     * @author 原点 467490186@qq.com
     */
    public function lower(): static
    {
        return new static(strtolower($this->value));
    }

    /**
     * Make a string uppercase
     * @return $this
     * @date 2025/11/14 下午6:00
     * @author 原点 467490186@qq.com
     */
    public function upper(): static
    {
        return new static(strtoupper($this->value));
    }

    /**
     * Strip whitespace (or other characters) from the beginning and end of a string
     * @param string $characters
     * @return $this
     * @date 2025/11/14 下午6:00
     * @author 原点 467490186@qq.com
     */
    public function trim(string $characters = ""): static
    {
        if ($characters) {
            return new static(trim($this->value, $characters));
        }
        return new static(trim($this->value));
    }

    /**
     * Strip whitespace (or other characters) from the beginning of a string
     * @return static
     */
    public function ltrim(): self
    {
        return new static(ltrim($this->value));
    }

    /**
     * Strip whitespace (or other characters) from the end of a string
     * @return static
     */
    public function rtrim(): self
    {
        return new static(rtrim($this->value));
    }

    /**
     * Return part of a string or false on failure. For PHP8.0+ only string is returned
     * @return static
     */
    public function substr(int $offset, ?int $length = null)
    {
        return new static(substr($this->value, $offset, $length));
    }

    /**
     * Repeat a string
     * @param int $times
     * @return $this
     * @date 2025/11/14 下午6:01
     * @author 原点 467490186@qq.com
     */
    public function repeat(int $times): static
    {
        return new static(str_repeat($this->value, $times));
    }

    public function append(mixed $str): static
    {
        return new static($this->value .= $str);
    }

    /**
     * Replace all occurrences of the search string with the replacement string
     * @param string $search
     * @param string $replace
     * @param int|null $count
     * @return $this
     */
    public function replace(string $search, string $replace, ?int &$count = null): static
    {
        return new static(str_replace($search, $replace, $this->value, $count));
    }

    /**
     * The function returns true if the passed $haystack starts from the $needle string or false otherwise.
     * @param string $needle
     * @return bool
     * @date 2025/11/14 下午6:02
     * @author 原点 467490186@qq.com
     */
    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->value, $needle);
    }

    /**
     * The function returns true if the passed $haystack ends with the $needle string or false otherwise.
     * @param string $needle
     * @return bool
     * @date 2025/11/14 下午6:03
     * @author 原点 467490186@qq.com
     */
    public function endsWith(string $needle): bool
    {
        return str_ends_with($this->value, $needle);
    }

    public function equals($str, bool $strict = false): bool
    {
        if ($str instanceof Str) {
            $str = strval($str);
        }
        if ($strict) {
            return $this->value === $str;
        }
        return $this->value == $str;
    }

    /**
     * Checks if $needle is found in $haystack and returns a boolean value
     * @param string $subString
     * @return bool
     * @date 2025/11/14 下午6:04
     * @author 原点 467490186@qq.com
     */
    public function contains(string $subString): bool
    {
        return str_contains($this->value, $subString);
    }

    /**
     * Split a string by a string
     * @param string $delimiter
     * @param int $limit
     * @return Arr
     * @date 2025/11/24 下午2:38
     * @author 原点 467490186@qq.com
     */
    public function split(string $delimiter, int $limit = PHP_INT_MAX): Arr
    {
        return static::detectArrayType(explode($delimiter, $this->value, $limit));
    }

    /**
     * @param int $index
     * @return string
     * @date 2025/11/24 下午2:39
     * @author 原点 467490186@qq.com
     */
    public function char(int $index): string
    {
        if ($index > strlen($this->value)) {
            return '';
        }
        return $this->value[$index];
    }

    /**
     * Get a new string object by splitting the string of current object into smaller chunks.
     * @param int $length
     * @param string $separator
     * @return $this
     * @see https://www.php.net/chunk_split
     */
    public function chunkSplit(int $length = 76, string $separator = "\r\n"): static
    {
        return new static(chunk_split($this->value, $length, $separator));
    }

    /**
     * Convert a string to an array object
     *
     * @param int $length Maximum length of the chunk.
     * @see https://www.php.net/str_split
     */
    public function chunk(int $length = 1): Arr
    {
        return static::detectArrayType(str_split($this->value, $length));
    }

    public function toString(): string
    {
        return $this->value;
    }

    protected static function detectArrayType(array $value): Arr
    {
        return new Arr($value);
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}