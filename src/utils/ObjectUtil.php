<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/18
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;

/**
 * 对象工具类
 */
class ObjectUtil
{
    /**
     * 是否为 null
     */
    public static function isNull(mixed $obj): bool
    {
        return $obj === null;
    }

    /**
     * 是否不为 null
     */
    public static function isNotNull(mixed $obj): bool
    {
        return $obj !== null;
    }

    /**
     * 是否为空（null、空字符串、空数组）
     */
    public static function isEmpty(mixed $obj): bool
    {
        return match (true) {
            $obj === null, $obj === '' => true,
            is_array($obj) => $obj === [],
            $obj instanceof \Countable => count($obj) === 0,
            default => false,
        };
    }

    /**
     * 是否不为空
     */
    public static function isNotEmpty(mixed $obj): bool
    {
        return !static::isEmpty($obj);
    }

    /**
     * null 安全的默认值
     */
    public static function defaultIfNull(mixed $obj, mixed $defaultValue): mixed
    {
        return $obj ?? $defaultValue;
    }

    /**
     * 空时返回默认值
     */
    public static function defaultIfEmpty(mixed $obj, mixed $defaultValue): mixed
    {
        return static::isEmpty($obj) ? $defaultValue : $obj;
    }

    /**
     * 空安全的 equals
     */
    public static function equals(mixed $obj1, mixed $obj2): bool
    {
        if ($obj1 === $obj2) {
            return true;
        }
        if ($obj1 === null || $obj2 === null) {
            return false;
        }
        if ($obj1 instanceof \DateTimeInterface && $obj2 instanceof \DateTimeInterface) {
            return $obj1 == $obj2;
        }
        return $obj1 == $obj2;
    }

    /**
     * 深度相等比较
     */
    public static function deepEquals(mixed $obj1, mixed $obj2): bool
    {
        if ($obj1 === $obj2) {
            return true;
        }
        if (gettype($obj1) !== gettype($obj2)) {
            return false;
        }
        if (is_array($obj1) && is_array($obj2)) {
            if (count($obj1) !== count($obj2)) {
                return false;
            }
            foreach ($obj1 as $key => $value) {
                if (!array_key_exists($key, $obj2) || !static::deepEquals($value, $obj2[$key])) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 序列化
     */
    public static function serialize(mixed $obj): string
    {
        return serialize($obj);
    }

    /**
     * 反序列化
     */
    public static function unserialize(string $str): mixed
    {
        return unserialize($str);
    }

    /**
     * 深度克隆
     */
    public static function clone(mixed $obj): mixed
    {
        if (!is_object($obj)) {
            return $obj;
        }
        return clone $obj;
    }

    /**
     * 深度克隆（支持嵌套对象）
     */
    public static function deepClone(mixed $obj): mixed
    {
        return unserialize(serialize($obj));
    }

    /**
     * 获取对象长度
     */
    public static function length(mixed $obj): int
    {
        return match (true) {
            is_string($obj) => mb_strlen($obj),
            is_array($obj), $obj instanceof \Countable => count($obj),
            default => 0,
        };
    }

    /**
     * 判断两个对象是否为同一类型
     */
    public static function isSameType(mixed $obj1, mixed $obj2): bool
    {
        return get_debug_type($obj1) === get_debug_type($obj2);
    }

    /**
     * 值转字符串（null 安全）
     */
    public static function toString(mixed $obj, string $nullStr = 'null'): string
    {
        if ($obj === null) {
            return $nullStr;
        }
        if (is_string($obj)) {
            return $obj;
        }
        if (is_scalar($obj)) {
            return (string)$obj;
        }
        if (is_array($obj) || is_object($obj)) {
            return json_encode($obj, JSON_UNESCAPED_UNICODE) ?: '';
        }
        return $nullStr;
    }
}
