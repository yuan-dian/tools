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
        // 普通车牌 (7位): 省份+字母+5位
        // 新能源车牌 (8位): 省份+字母+6位
        return (bool)preg_match(
            '/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤川青藏琼宁][A-HJ-NP-Z][A-HJ-NP-Z0-9]{5,6}$/u',
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

    // ==================== 基础类型检查 ====================

    /**
     * 是否为字母和数字
     */
    public static function isAlphaNumeric(string $str): bool
    {
        return $str !== '' && ctype_alnum($str);
    }

    /**
     * 是否为整数
     */
    public static function isInteger(string $str): bool
    {
        return $str !== '' && preg_match('/^-?\d+$/', $str);
    }

    /**
     * 是否为浮点数
     */
    public static function isFloat(string $str): bool
    {
        return is_numeric($str) && str_contains($str, '.');
    }

    /**
     * 是否为布尔值
     */
    public static function isBoolean(string $str): bool
    {
        return in_array(strtolower($str), ['true', 'false', '1', '0', 'yes', 'no'], true);
    }

    /**
     * 是否为JSON
     */
    public static function isJson(string $str): bool
    {
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * 是否为Base64
     */
    public static function isBase64(string $str): bool
    {
        return $str !== '' && base64_encode(base64_decode($str, true)) === $str;
    }

    /**
     * 是否为十六进制
     */
    public static function isHex(string $str): bool
    {
        return $str !== '' && ctype_xdigit($str);
    }

    /**
     * 是否为二进制
     */
    public static function isBinary(string $str): bool
    {
        return $str !== '' && preg_match('/^[01]+$/', $str);
    }

    // ==================== 日期时间检查 ====================

    /**
     * 是否为日期
     */
    public static function isDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * 是否为时间
     */
    public static function isTime(string $time, string $format = 'H:i:s'): bool
    {
        $d = \DateTime::createFromFormat($format, $time);
        return $d && $d->format($format) === $time;
    }

    // ==================== 证件检查 ====================

    /**
     * 是否为银行卡号 (Luhn算法)
     */
    public static function isBankCard(string $card): bool
    {
        if (!preg_match('/^\d{16,19}$/', $card)) {
            return false;
        }
        $sum = 0;
        $length = strlen($card);
        for ($i = $length - 1, $j = 0; $i >= 0; $i--, $j++) {
            $digit = (int)$card[$i];
            if ($j % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }
        return $sum % 10 === 0;
    }

    /**
     * 是否为护照号 (中国护照)
     */
    public static function isPassport(string $passport): bool
    {
        return (bool)preg_match('/^[EeGg]\d{8}$/', $passport);
    }

    /**
     * 是否为军官证号
     */
    public static function isMilitaryId(string $id): bool
    {
        return (bool)preg_match('/^[\u4e00-\u9fa5]{7}$|^\d{8}$/', $id);
    }

    // ==================== 字符串格式检查 ====================

    /**
     * 是否为合法的URL slug
     */
    public static function isSlug(string $str): bool
    {
        return (bool)preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $str);
    }

    /**
     * 是否为语义化版本 (SemVer)
     */
    public static function isSemver(string $version): bool
    {
        return (bool)preg_match(
            '/^\d+\.\d+\.\d+(?:-[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*)?(?:\+[a-zA-Z0-9]+(?:\.[a-zA-Z0-9]+)*)?$/',
            $version
        );
    }

    /**
     * 是否为十六进制颜色
     */
    public static function isHexColor(string $color): bool
    {
        return (bool)preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color);
    }

    /**
     * 是否为RGB颜色
     */
    public static function isRgbColor(string $color): bool
    {
        return (bool)preg_match('/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i', $color, $matches)
            && $matches[1] <= 255 && $matches[2] <= 255 && $matches[3] <= 255;
    }

    /**
     * 是否为ASCII字符串
     */
    public static function isAscii(string $str): bool
    {
        return $str === '' || preg_match('/^[\x20-\x7E]*$/', $str);
    }

    /**
     * 是否包含多字节字符
     */
    public static function isMultibyte(string $str): bool
    {
        return $str !== '' && mb_strlen($str) !== strlen($str);
    }

    /**
     * 是否为强密码 (至少8位，包含大小写、数字、特殊字符)
     */
    public static function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[a-z]/', $password)
            && preg_match('/[A-Z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[^a-zA-Z0-9]/', $password);
    }

    /**
     * 是否为合法的域名
     */
    public static function isDomain(string $domain): bool
    {
        return (bool)preg_match('/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $domain);
    }

    /**
     * 是否为合法的端口号
     */
    public static function isPort(string $port): bool
    {
        return ctype_digit($port) && (int)$port >= 0 && (int)$port <= 65535;
    }

    // ==================== 范围检查 ====================

    /**
     * 是否在范围内 (字符串长度)
     */
    public static function isLengthInRange(string $str, int $min, int $max): bool
    {
        $len = mb_strlen($str);
        return $len >= $min && $len <= $max;
    }

    /**
     * 是否在范围内 (数组大小)
     */
    public static function isSizeInRange(array $array, int $min, int $max): bool
    {
        $size = count($array);
        return $size >= $min && $size <= $max;
    }

    // ==================== 批量验证 ====================

    /**
     * 批量校验 (返回所有错误)
     * @param array $data 数据
     * @param array $rules 规则 ['field' => ['rule' => 'message']]
     * @return array 错误列表 ['field' => 'message']
     */
    public static function validateAll(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $rule => $message) {
                if (!self::checkRule($value, $rule)) {
                    $errors[$field] = $message;
                    break;
                }
            }
        }
        return $errors;
    }

    /**
     * 检查是否通过校验
     */
    private static function checkRule(mixed $value, string $rule): bool
    {
        return match ($rule) {
            'required' => $value !== null && $value !== '',
            'email' => self::isEmail((string)$value),
            'mobile' => self::isMobile((string)$value),
            'url' => self::isUrl((string)$value),
            'numeric' => is_numeric($value),
            'integer' => self::isInteger((string)$value),
            'alpha' => self::isLetter((string)$value),
            'alpha_num' => self::isAlphaNumeric((string)$value),
            'json' => self::isJson((string)$value),
            'date' => self::isDate((string)$value),
            'ip' => self::isIP((string)$value),
            'uuid' => self::isUUID((string)$value),
            default => true,
        };
    }

    /**
     * 校验并返回结果
     */
    public static function tryValidate(mixed $value, string $rule, string $message = ''): bool
    {
        $valid = self::checkRule($value, $rule);
        if (!$valid && $message !== '') {
            throw new \InvalidArgumentException($message);
        }
        return $valid;
    }

}
