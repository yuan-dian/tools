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


    public function length(): int
    {
        return strlen($this->value);
    }

    public function indexOf(string $needle, int $offset = 0): false|int
    {
        return strpos($this->value, $needle, $offset);
    }

    public function lastIndexOf(string $needle, int $offset = 0): false|int
    {
        return strrpos($this->value, $needle, $offset);
    }

    public function pos(string $needle, int $offset = 0): false|int
    {
        return strpos($this->value, $needle, $offset);
    }

    public function rpos(string $needle, int $offset = 0): false|int
    {
        return strrpos($this->value, $needle, $offset);
    }

    public function reverse(): static
    {
        return new static(strrev($this->value));
    }

    /**
     * @return false|int
     */
    public function ipos(string $needle): bool|int
    {
        return stripos($this->value, $needle);
    }

    public function lower(): static
    {
        return new static(strtolower($this->value));
    }

    public function upper(): static
    {
        return new static(strtoupper($this->value));
    }

    public function trim(string $characters = ''): static
    {
        if ($characters) {
            return new static(trim($this->value, $characters));
        }
        return new static(trim($this->value));
    }

    /**
     * @return static
     */
    public function ltrim(): self
    {
        return new static(ltrim($this->value));
    }

    /**
     * @return static
     */
    public function rtrim(): self
    {
        return new static(rtrim($this->value));
    }

    /**
     * @return static
     */
    public function substr(int $offset, ?int $length = null)
    {
        return new static(substr($this->value, $offset, $length));
    }

    public function repeat(int $times): static
    {
        return new static(str_repeat($this->value, $times));
    }

    public function append(mixed $str): static
    {
        return new static($this->value .= $str);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param int|null $count
     * @return $this
     */
    public function replace(string $search, string $replace, ?int &$count = null): static
    {
        return new static(str_replace($search, $replace, $this->value, $count));
    }

    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->value, $needle);
    }

    public function endsWith(string $needle): bool
    {
        return strrpos($this->value, $needle) === (strlen($this->value) - strlen($needle));
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

    public function contains(string $subString): bool
    {
        return str_contains($this->value, $subString);
    }

    public function split(string $delimiter, int $limit = PHP_INT_MAX): Arr
    {
        return static::detectArrayType(explode($delimiter, $this->value, $limit));
    }

    public function char(int $index): string
    {
        if ($index > strlen($this->value)) {
            return '';
        }
        return $this->value[$index];
    }

    /**
     * Get a new string object by splitting the string of current object into smaller chunks.
     *
     * @param int $length The chunk length.
     * @param string $separator The line ending sequence.
     * @see https://www.php.net/chunk_split
     */
    public function chunkSplit(int $length = 76, string $separator = "\r\n"): static
    {
        return new static(chunk_split($this->value, $length, $separator));
    }

    /**
     * Convert a string to an array object of class \Swoole\ArrayObject.
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