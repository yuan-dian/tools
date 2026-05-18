<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/7/23
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;

class StrUtil
{
    private const DELIM_START = '{';
    private const DELIM_STOP = '}';
    private const ESCAPE_CHAR = '\\';
    private const  FORMAT_SPECIFIER = '/%(\d+\$)?([-#+ 0,(<]*)?(\d+)?(\.\d+)?([tT])?([a-zA-Z%])/';
    protected static array $snakeCache = [];

    protected static array $camelCache = [];

    protected static array $studlyCache = [];

    /**
     * 判断字符串是否为空 (null/空字符串)
     * @param string|null $str
     * @return bool
     * @date 2025/7/23 上午10:47
     * @author 原点 467490186@qq.com
     */
    public static function isEmpty(?string $str): bool
    {
        return $str === null || $str === '';
    }

    /**
     * 判断字符串是否非空
     * @param string|null $str
     * @return bool
     * @date 2025/7/23 上午10:47
     * @author 原点 467490186@qq.com
     */
    public static function isNotEmpty(?string $str): bool
    {
        return !self::isEmpty($str);
    }

    /**
     * 判断字符串是否为空白 (空或仅包含空白字符)
     * @param string|null $str
     * @return bool
     * @date 2025/7/23 上午10:46
     * @author 原点 467490186@qq.com
     */
    public static function isBlank(?string $str): bool
    {
        if ($str === null || $str === '') {
            return true;
        }
        return ctype_space($str);
    }

    /**
     * 判断字符串是否非空白
     * @param string|null $str
     * @return bool
     * @date 2025/7/23 上午10:46
     * @author 原点 467490186@qq.com
     */
    public static function isNotBlank(?string $str): bool
    {
        return !self::isBlank($str);
    }

    /**
     * 移除首尾空白字符 (支持null)
     * @param string|null $str
     * @return string
     * @date 2025/7/23 上午10:46
     * @author 原点 467490186@qq.com
     */
    public static function trim(?string $str): string
    {
        return $str === null ? '' : trim($str);
    }

    /**
     * 移除首尾空白并处理null
     * @param string|null $str
     * @return string
     * @date 2025/7/23 上午10:46
     * @author 原点 467490186@qq.com
     */
    public static function trimToEmpty(?string $str): string
    {
        return $str === null ? '' : trim($str);
    }

    /**
     * 移除首尾空白，结果为空白则返回null
     * @param string|null $str
     * @return string|null
     * @date 2025/7/23 上午10:45
     * @author 原点 467490186@qq.com
     */
    public static function trimToNull(?string $str): ?string
    {
        if ($str === null) {
            return null;
        }
        $trimmed = trim($str);
        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * 字符串占位符处理
     * 包含printf风格和{}占位符两种模式的处理
     * @param string $message
     * @param ...$args
     * @return string
     * @date 2025/7/23 上午10:45
     * @author 原点 467490186@qq.com
     */
    public static function format(string $message, ...$args): string
    {
        if (preg_match(self::FORMAT_SPECIFIER, $message)) {
            return sprintf($message, ...$args);
        }
        $result = '';
        $length = strlen($message);
        $argIndex = 0;
        $argCount = count($args);

        for ($i = 0; $i < $length; $i++) {
            if ($message[$i] === self::DELIM_START &&
                $i + 1 < $length &&
                $message[$i + 1] === self::DELIM_STOP) {
                if ($argIndex < $argCount) {
                    $result .= $args[$argIndex++];
                    $i++;
                } else {
                    $result .= self::DELIM_START . self::DELIM_STOP;
                    $i++;
                }
            } elseif ($message[$i] === self::ESCAPE_CHAR &&
                $i + 1 < $length &&
                $message[$i + 1] === self::DELIM_START) {
                $result .= self::DELIM_START;
                $i++;
            } else {
                $result .= $message[$i];
            }
        }
        return $result;
    }

    /**
     * 检查字符串中是否包含某些字符串
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function contains(string $haystack, array|string $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串结尾
     *
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function endsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if (str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串开头
     *
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function startsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if (str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }


    /**
     * 字符串转小写
     *
     * @param string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * 获取字符串的长度
     *
     * @param string $value
     * @return int
     */
    public static function length(string $value): int
    {
        return mb_strlen($value);
    }

    /**
     * 截取字符串
     *
     * @param string $str 原字符串
     * @param int $fromIndex 起始位置（支持负数，表示从末尾开始）
     * @param int|null $toIndex 结束位置（支持负数），null 表示到末尾
     * @return string
     */
    public static function sub(string $str, int $fromIndex, ?int $toIndex = null): string
    {
        $len = mb_strlen($str);
        if ($fromIndex < 0) {
            $fromIndex += $len;
        }
        if ($toIndex === null) {
            $toIndex = $len;
        }
        if ($toIndex < 0) {
            $toIndex += $len;
        }
        $fromIndex = max(0, min($fromIndex, $len));
        $toIndex = max(0, min($toIndex, $len));

        if ($fromIndex >= $toIndex) {
            return '';
        }

        return mb_substr($str, $fromIndex, $toIndex - $fromIndex);
    }

    /**
     * 驼峰转下划线
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * 下划线转驼峰(首字母小写)
     *
     * @param string $value
     * @return string
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * 下划线转驼峰(首字母大写)
     *
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * 转为首字母大写的标题格式
     *
     * @param string $value
     * @return string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * 首字母大写
     */
    public static function upperFirst(string $str): string
    {
        if (static::isEmpty($str)) {
            return $str;
        }
        return ucfirst($str);
    }

    /**
     * 首字母小写
     */
    public static function lowerFirst(string $str): string
    {
        if (static::isEmpty($str)) {
            return $str;
        }
        return lcfirst($str);
    }

    /**
     * 左填充
     */
    public static function padBefore(string $str, int $length, string $padStr = ' '): string
    {
        return str_pad($str, $length, $padStr, STR_PAD_LEFT);
    }

    /**
     * 右填充
     */
    public static function padAfter(string $str, int $length, string $padStr = ' '): string
    {
        return str_pad($str, $length, $padStr, STR_PAD_RIGHT);
    }

    /**
     * 是否为纯数字
     */
    public static function isNumeric(string $str): bool
    {
        return $str !== '' && ctype_digit($str);
    }

    /**
     * 是否为纯字母
     */
    public static function isAlpha(string $str): bool
    {
        return $str !== '' && ctype_alpha($str);
    }

    /**
     * 是否为字母和数字
     */
    public static function isAlphaNumeric(string $str): bool
    {
        return $str !== '' && ctype_alnum($str);
    }

    /**
     * 是否全为中文字符
     */
    public static function isChinese(string $str): bool
    {
        return (bool)preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str);
    }

    /**
     * 是否包含中文字符
     */
    public static function containsChinese(string $str): bool
    {
        return (bool)preg_match('/[\x{4e00}-\x{9fa5}]/u', $str);
    }

    /**
     * 空安全的字符串比较
     */
    public static function equals(?string $str1, ?string $str2, bool $ignoreCase = false): bool
    {
        if ($str1 === $str2) {
            return true;
        }
        if ($str1 === null || $str2 === null) {
            return false;
        }
        return $ignoreCase ? strcasecmp($str1, $str2) === 0 : $str1 === $str2;
    }

}