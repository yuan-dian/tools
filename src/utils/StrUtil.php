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
        return $str === null || trim($str) === '';
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
     * @param string       $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function contains(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串结尾
     *
     * @param  string       $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function endsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串开头
     *
     * @param  string       $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function startsWith(string $haystack, array|string $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }


    /**
     * 字符串转小写
     *
     * @param  string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param  string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * 获取字符串的长度
     *
     * @param  string $value
     * @return int
     */
    public static function length(string $value): int
    {
        return mb_strlen($value);
    }

    /**
     * 截取字符串
     *
     * @param  string   $string
     * @param  int      $start
     * @param  int|null $length
     * @return string
     */
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * 驼峰转下划线
     *
     * @param  string $value
     * @param  string $delimiter
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
     * @param  string $value
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
     * @param  string $value
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
     * @param  string $value
     * @return string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }


}