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

class ValidatorUtil
{
    /**
     * 是否为邮箱
     */
    public static function isEmail(string $email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 是否为中国手机号
     */
    public static function isMobile(string $mobile): bool
    {
        return (bool)preg_match('/^1[3-9]\d{9}$/', $mobile);
    }

    /**
     * 是否为URL
     */
    public static function isUrl(string $url): bool
    {
        return (bool)filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * 是否为IP地址
     */
    public static function isIP(string $ip): bool
    {
        return (bool)filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * 是否为 IPv4
     */
    public static function isIPv4(string $ip): bool
    {
        return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * 是否为 IPv6
     */
    public static function isIPv6(string $ip): bool
    {
        return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * 是否为UUID
     */
    public static function isUUID(string $str): bool
    {
        return (bool)preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $str);
    }

    /**
     * 是否为MAC地址
     */
    public static function isMac(string $mac): bool
    {
        return (bool)preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac);
    }

    /**
     * 是否为身份证号（支持18位）
     */
    public static function isIdCard(string $idCard): bool
    {
        $idCard = strtoupper(trim($idCard));

        if (!preg_match('/^\d{17}[\dX]$/', $idCard)) {
            return false;
        }

        // 加权因子
        $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        // 校验码
        $checkCodes = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += (int)$idCard[$i] * $weights[$i];
        }

        return $checkCodes[$sum % 11] === $idCard[17];
    }

    /**
     * 是否为中国统一社会信用代码
     */
    public static function isCreditCode(string $code): bool
    {
        return (bool)preg_match('/^[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10}$/', $code);
    }

    /**
     * 是否为中文字符
     */
    public static function isChinese(string $str): bool
    {
        return (bool)preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str);
    }

    /**
     * 包含中文
     */
    public static function hasChinese(string $str): bool
    {
        return (bool)preg_match('/[\x{4e00}-\x{9fa5}]/u', $str);
    }

    /**
     * 是否为出生日期
     */
    public static function isBirthday(string $date): bool
    {
        if (!preg_match('/^\d{4}[-\/](0[1-9]|1[0-2])[-\/](0[1-9]|[12]\d|3[01])$/', $date)) {
            return false;
        }
        $parts = preg_split('/[-\/]/', $date);
        return checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
    }

    /**
     * 是否为车牌号
     */
    public static function isCarLicense(string $license): bool
    {
        // 普通车牌 + 新能源车牌
        return (bool)preg_match(
            '/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤川青藏琼宁][A-HJ-NP-Z][A-HJ-NP-Z0-9]{4,5}[A-HJ-NP-Z0-9挂学警港澳]$/',
            $license
        );
    }

    /**
     * 是否为邮政编码
     */
    public static function isZipCode(string $zipCode): bool
    {
        return (bool)preg_match('/^\d{6}$/', $zipCode);
    }

    /**
     * 是否为汉字、字母或数字
     */
    public static function isGeneral(string $str): bool
    {
        return (bool)preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/u', $str);
    }

    /**
     * 是否为数字
     */
    public static function isNumeric(string $str): bool
    {
        return is_numeric($str);
    }

    /**
     * 是否为字母
     */
    public static function isLetter(string $str): bool
    {
        return $str !== '' && ctype_alpha($str);
    }

    /**
     * 是否为合法的日期时间字符串
     */
    public static function isDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): bool
    {
        $d = \DateTime::createFromFormat($format, $dateTime);
        return $d && $d->format($format) === $dateTime;
    }

    /**
     * 范围校验
     */
    public static function isBetween(int|float $value, int|float $min, int|float $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    /**
     * 长度校验
     */
    public static function isLengthBetween(string $str, int $min, int $max): bool
    {
        $len = mb_strlen($str);
        return $len >= $min && $len <= $max;
    }

    /**
     * 匹配正则
     */
    public static function match(string $regex, string $str): bool
    {
        return (bool)preg_match($regex, $str);
    }

    /**
     * 校验并抛异常
     *
     * @throws \InvalidArgumentException
     */
    public static function validate(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * 非空校验
     *
     * @throws \InvalidArgumentException
     */
    public static function notEmpty(mixed $value, string $name = 'value'): void
    {
        if (ObjectUtil::isEmpty($value)) {
            throw new \InvalidArgumentException("{$name} must not be empty");
        }
    }

    /**
     * 非 null 校验
     *
     * @throws \InvalidArgumentException
     */
    public static function notNull(mixed $value, string $name = 'value'): void
    {
        if ($value === null) {
            throw new \InvalidArgumentException("{$name} must not be null");
        }
    }

    /**
     * 邮箱校验（失败抛异常）
     *
     * @throws \InvalidArgumentException
     */
    public static function validateEmail(string $email): void
    {
        if (!static::isEmail($email)) {
            throw new \InvalidArgumentException("Invalid email: {$email}");
        }
    }

    /**
     * 手机号校验（失败抛异常）
     *
     * @throws \InvalidArgumentException
     */
    public static function validateMobile(string $mobile): void
    {
        if (!static::isMobile($mobile)) {
            throw new \InvalidArgumentException("Invalid mobile: {$mobile}");
        }
    }
}
