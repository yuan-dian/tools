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

    /**
     * 重复字符串
     * @param string $str
     * @param int $count
     * @return string
     */
    public static function repeat(string $str, int $count): string
    {
        if ($count <= 0) {
            return '';
        }
        return str_repeat($str, $count);
    }

    /**
     * 反转字符串
     * @param string $str
     * @return string
     */
    public static function reverse(string $str): string
    {
        $result = '';
        $length = mb_strlen($str);
        for ($i = $length - 1; $i >= 0; $i--) {
            $result .= mb_substr($str, $i, 1);
        }
        return $result;
    }

    /**
     * 移除子串 (只移除第一个匹配)
     * @param string $str
     * @param string $search
     * @return string
     */
    public static function remove(string $str, string $search): string
    {
        return str_replace($search, '', $str);
    }

    /**
     * 移除所有匹配子串
     * @param string $str
     * @param string $search
     * @return string
     */
    public static function removeAll(string $str, string $search): string
    {
        return str_replace($search, '', $str);
    }

    /**
     * 替换子串
     * @param string $str
     * @param string $search
     * @param string $replace
     * @return string
     */
    public static function replace(string $str, string $search, string $replace): string
    {
        return str_replace($search, $replace, $str);
    }

    /**
     * 按索引替换子串
     * @param string $str
     * @param int $start
     * @param int|null $length
     * @param string $replace
     * @return string
     */
    public static function replaceRange(string $str, int $start, ?int $length, string $replace): string
    {
        $len = mb_strlen($str);
        if ($start < 0) {
            $start += $len;
        }
        if ($length === null) {
            $length = $len - $start;
        }
        if ($length < 0) {
            $length = $len - $start + $length;
        }
        $start = max(0, min($start, $len));
        $length = max(0, min($length, $len - $start));
        return mb_substr($str, 0, $start) . $replace . mb_substr($str, $start + $length);
    }

    /**
     * 截断缩写 (超过长度时加省略号)
     * @param string $str
     * @param int $maxLength
     * @param string $suffix
     * @return string
     */
    public static function abbreviate(string $str, int $maxLength, string $suffix = '...'): string
    {
        if (mb_strlen($str) <= $maxLength) {
            return $str;
        }
        $suffixLen = mb_strlen($suffix);
        return mb_substr($str, 0, $maxLength - $suffixLen) . $suffix;
    }

    /**
     * 空时返回默认值
     * @param string|null $str
     * @param string $default
     * @return string
     */
    public static function defaultIfEmpty(?string $str, string $default): string
    {
        return self::isEmpty($str) ? $default : $str;
    }

    /**
     * 空白时返回默认值
     * @param string|null $str
     * @param string $default
     * @return string
     */
    public static function defaultIfBlank(?string $str, string $default): string
    {
        return self::isBlank($str) ? $default : $str;
    }

    /**
     * 计算子串出现次数
     * @param string $haystack
     * @param string $needle
     * @return int
     */
    public static function count(string $haystack, string $needle): int
    {
        if ($needle === '') {
            return 0;
        }
        return mb_substr_count($haystack, $needle);
    }

    /**
     * 查找子串位置 (从0开始，找不到返回-1)
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @return int
     */
    public static function indexOf(string $haystack, string $needle, int $offset = 0): int
    {
        $pos = mb_strpos($haystack, $needle, $offset);
        return $pos === false ? -1 : $pos;
    }

    /**
     * 查找子串最后出现位置 (从0开始，找不到返回-1)
     * @param string $haystack
     * @param string $needle
     * @return int
     */
    public static function lastIndexOf(string $haystack, string $needle): int
    {
        $pos = mb_strrpos($haystack, $needle);
        return $pos === false ? -1 : $pos;
    }

    /**
     * 居中填充
     * @param string $str
     * @param int $length
     * @param string $padStr
     * @return string
     */
    public static function center(string $str, int $length, string $padStr = ' '): string
    {
        return str_pad($str, $length, $padStr, STR_PAD_BOTH);
    }

    /**
     * 包裹字符串
     * @param string $str
     * @param string $before
     * @param string $after
     * @return string
     */
    public static function wrap(string $str, string $before, ?string $after = null): string
    {
        if ($after === null) {
            $after = $before;
        }
        return $before . $str . $after;
    }

    /**
     * 大小写互换
     * @param string $str
     * @return string
     */
    public static function swapCase(string $str): string
    {
        $result = '';
        $length = mb_strlen($str);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($str, $i, 1);
            if (mb_strtolower($char) === $char) {
                $result .= mb_strtoupper($char);
            } else {
                $result .= mb_strtolower($char);
            }
        }
        return $result;
    }

    /**
     * 左截取
     * @param string $str
     * @param int $len
     * @return string
     */
    public static function left(string $str, int $len): string
    {
        return mb_substr($str, 0, $len);
    }

    /**
     * 右截取
     * @param string $str
     * @param int $len
     * @return string
     */
    public static function right(string $str, int $len): string
    {
        $strLen = mb_strlen($str);
        return mb_substr($str, max(0, $strLen - $len));
    }

    /**
     * 中间截取
     * @param string $str
     * @param int $pos
     * @param int $len
     * @return string
     */
    public static function mid(string $str, int $pos, int $len): string
    {
        if ($len <= 0 || $pos >= mb_strlen($str)) {
            return '';
        }
        return mb_substr($str, $pos, $len);
    }

    /**
     * 截断字符串
     * @param string $str
     * @param int $maxLength
     * @param string $suffix
     * @return string
     */
    public static function truncate(string $str, int $maxLength, string $suffix = ''): string
    {
        if (mb_strlen($str) <= $maxLength) {
            return $str;
        }
        return mb_substr($str, 0, $maxLength) . $suffix;
    }

    /**
     * 前缀不存在时添加
     * @param string $str
     * @param string $prefix
     * @return string
     */
    public static function prependIfMissing(string $str, string $prefix): string
    {
        return self::startsWith($str, $prefix) ? $str : $prefix . $str;
    }

    /**
     * 后缀不存在时添加
     * @param string $str
     * @param string $suffix
     * @return string
     */
    public static function appendIfMissing(string $str, string $suffix): string
    {
        return self::endsWith($str, $suffix) ? $str : $str . $suffix;
    }

    /**
     * 移除前缀
     * @param string $str
     * @param string $prefix
     * @return string
     */
    public static function removeStart(string $str, string $prefix): string
    {
        return self::startsWith($str, $prefix) ? mb_substr($str, mb_strlen($prefix)) : $str;
    }

    /**
     * 移除后缀
     * @param string $str
     * @param string $suffix
     * @return string
     */
    public static function removeEnd(string $str, string $suffix): string
    {
        return self::endsWith($str, $suffix) ? mb_substr($str, 0, mb_strlen($str) - mb_strlen($suffix)) : $str;
    }

    /**
     * 转为kebab-case
     * @param string $str
     * @return string
     */
    public static function kebab(string $str): string
    {
        return str_replace('_', '-', self::snake($str));
    }

    /**
     * 判断是否为纯空格
     * @param string $str
     * @return bool
     */
    public static function isWhitespace(string $str): bool
    {
        return $str !== '' && ctype_space($str);
    }

    /**
     * 截取两个标记之间的内容
     * @param string $str
     * @param string $start
     * @param string $end
     * @return string|null
     */
    public static function substringBetween(string $str, string $start, string $end): ?string
    {
        $startPos = mb_strpos($str, $start);
        if ($startPos === false) {
            return null;
        }
        $startPos += mb_strlen($start);
        $endPos = mb_strpos($str, $end, $startPos);
        if ($endPos === false) {
            return null;
        }
        return mb_substr($str, $startPos, $endPos - $startPos);
    }

    /**
     * 分割字符串
     * @param string $str
     * @param string $separator
     * @return array
     */
    public static function split(string $str, string $separator): array
    {
        return explode($separator, $str);
    }

    /**
     * 连接数组为字符串
     * @param array $array
     * @param string $separator
     * @return string
     */
    public static function join(array $array, string $separator = ','): string
    {
        return implode($separator, $array);
    }

    /**
     * 是否匹配正则
     * @param string $str
     * @param string $regex
     * @return bool
     */
    public static function matches(string $str, string $regex): bool
    {
        return (bool)preg_match($regex, $str);
    }

    /**
     * 是否全部为数字 (支持负数和小数)
     * @param string $str
     * @return bool
     */
    public static function isNumericStrict(string $str): bool
    {
        return is_numeric($str);
    }

    /**
     * 字符串哈希 (返回整数)
     * @param string $str
     * @return int
     */
    public static function hash(string $str): int
    {
        return crc32($str);
    }

    /**
     * 随机字符串
     * @param int $length
     * @param string $chars
     * @return string
     */
    public static function random(
        int $length,
        string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    ): string {
        $result = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }
        return $result;
    }

}