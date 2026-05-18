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

use InvalidArgumentException;

/**
 * 数字工具类
 */
class NumberUtil
{
    /** 默认精度 */
    private const DEFAULT_SCALE = 2;

    /**
     * 是否为数字（包括字符串形式）
     */
    public static function isNumber(mixed $value): bool
    {
        if (is_int($value) || is_float($value)) {
            return true;
        }
        if (!is_string($value)) {
            return false;
        }
        return (bool)preg_match('/^[+-]?(\d+\.?\d*|\.\d+)([eE][+-]?\d+)?$/', trim($value));
    }

    /**
     * 是否为整数
     */
    public static function isInteger(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        }
        if (is_string($value)) {
            return (bool)preg_match('/^[+-]?\d+$/', trim($value));
        }
        return false;
    }

    /**
     * 是否为长整型范围
     */
    public static function isLong(mixed $value): bool
    {
        if (!static::isInteger($value)) {
            return false;
        }
        $int = (int)$value;
        return $int >= PHP_INT_MIN && $int <= PHP_INT_MAX;
    }

    /**
     * 是否为浮点数
     */
    public static function isDouble(mixed $value): bool
    {
        return is_float($value) || (is_string($value) && (bool)preg_match('/^[+-]?\d+\.\d+$/', trim($value)));
    }

    /**
     * 是否为偶数
     */
    public static function isEven(int $number): bool
    {
        return ($number & 1) === 0;
    }

    /**
     * 是否为奇数
     */
    public static function isOdd(int $number): bool
    {
        return ($number & 1) === 1;
    }

    /**
     * 是否为质数
     */
    public static function isPrimes(int $n): bool
    {
        if ($n <= 1) {
            return false;
        }
        if ($n <= 3) {
            return true;
        }
        if ($n % 2 === 0 || $n % 3 === 0) {
            return false;
        }
        for ($i = 5; $i * $i <= $n; $i += 6) {
            if ($n % $i === 0 || $n % ($i + 2) === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 解析为整数（安全）
     */
    public static function parseInt(mixed $value, int $defaultValue = 0): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return (int)$value;
        }
        if (is_string($value)) {
            $value = trim($value);
            if ($value !== '' && preg_match('/^[+-]?\d+$/', $value)) {
                return (int)$value;
            }
        }
        return $defaultValue;
    }

    /**
     * 解析为浮点数（安全）
     */
    public static function parseFloat(mixed $value, float $defaultValue = 0.0): float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }
        return $defaultValue;
    }

    /**
     * 加法（精度安全）
     */
    public static function add(float|int|string $a, float|int|string $b, int $scale = self::DEFAULT_SCALE): float
    {
        return (float)bcadd((string)$a, (string)$b, $scale);
    }

    /**
     * 减法
     */
    public static function sub(float|int|string $a, float|int|string $b, int $scale = self::DEFAULT_SCALE): float
    {
        return (float)bcsub((string)$a, (string)$b, $scale);
    }

    /**
     * 乘法
     */
    public static function mul(float|int|string $a, float|int|string $b, int $scale = self::DEFAULT_SCALE): float
    {
        return (float)bcmul((string)$a, (string)$b, $scale);
    }

    /**
     * 除法
     */
    public static function div(float|int|string $a, float|int|string $b, int $scale = self::DEFAULT_SCALE): float
    {
        if ($b == 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return (float)bcdiv((string)$a, (string)$b, $scale);
    }

    /**
     * 取余
     */
    public static function mod(int $a, int $b): int
    {
        if ($b === 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return $a % $b;
    }

    /**
     * 向上取整除法
     */
    public static function ceilingDiv(int $a, int $b): int
    {
        if ($b === 0) {
            throw new InvalidArgumentException('Division by zero');
        }
        return (int)ceil($a / $b);
    }

    /**
     * 四舍五入
     */
    public static function round(
        float $number,
        int $scale = self::DEFAULT_SCALE,
        int $roundingMode = PHP_ROUND_HALF_UP
    ): float {
        return round($number, $scale, $roundingMode);
    }

    /**
     * 四舍五入为字符串（保证精度）
     */
    public static function roundStr(float $number, int $scale = self::DEFAULT_SCALE): string
    {
        return number_format($number, $scale, '.', '');
    }

    /**
     * 取最小值
     */
    public static function min(int|float ...$numbers): int|float
    {
        return min($numbers);
    }

    /**
     * 取最大值
     */
    public static function max(int|float ...$numbers): int|float
    {
        return max($numbers);
    }

    /**
     * 数字比较
     */
    public static function compare(int|float $a, int|float $b): int
    {
        if ($a === $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    /**
     * null 安全比较
     */
    public static function compareNullSafe(?int $a, ?int $b): int
    {
        if ($a === $b) {
            return 0;
        }
        if ($a === null) {
            return -1;
        }
        if ($b === null) {
            return 1;
        }
        return ($a < $b) ? -1 : 1;
    }

    /**
     * 限制在范围内
     */
    public static function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        return max($min, min($max, $value));
    }

    /**
     * null 转 0
     */
    public static function nullToZero(?int $value): int
    {
        return $value ?? 0;
    }

    /**
     * 生成范围内随机整数 [min, max]
     */
    public static function generateBetween(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /**
     * 生成范围内整数序列
     *
     * @param int $start 起始值
     * @param int $end 结束值
     * @param int $step 步长
     * @return int[]
     */
    public static function range(int $start, int $end, int $step = 1): array
    {
        return range($start, $end, $step);
    }

    /**
     * 阶乘
     */
    public static function factorial(int $n): int
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Factorial is not defined for negative numbers');
        }
        $result = 1;
        for ($i = 2; $i <= $n; $i++) {
            $result *= $i;
        }
        return $result;
    }

    /**
     * 最大公约数
     */
    public static function gcd(int $a, int $b): int
    {
        $a = abs($a);
        $b = abs($b);
        while ($b !== 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }

    /**
     * 最小公倍数
     */
    public static function lcm(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }
        return abs($a * $b) / static::gcd($a, $b);
    }

    /**
     * 幂运算（精度安全）
     */
    public static function pow(int|float $base, int $exponent, int $scale = self::DEFAULT_SCALE): float
    {
        return (float)bcpow((string)$base, (string)$exponent, $scale);
    }

    /**
     * 平方根（精度安全）
     */
    public static function sqrt(float $value, int $scale = self::DEFAULT_SCALE): float
    {
        return (float)bcsqrt((string)$value, $scale);
    }

    /**
     * 数字格式化（千分位）
     */
    public static function format(int|float $number, int $decimals = 0): string
    {
        return number_format($number, $decimals, '.', ',');
    }

    /**
     * 将数字转换为中文（仅整数，支持到亿）
     */
    public static function toChinese(int $number): string
    {
        if ($number === 0) {
            return '零';
        }

        $negative = $number < 0;
        $number = abs($number);

        $digits = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
        $units = ['', '十', '百', '千'];
        $sections = ['', '万', '亿'];

        $result = '';
        $sectionIndex = 0;

        while ($number > 0) {
            $section = $number % 10000;
            $sectionStr = '';
            $hasOutput = false;

            for ($i = 0; $i < 4; $i++) {
                $digit = $section % 10;
                if ($digit !== 0) {
                    if ($hasOutput) {
                        $sectionStr = '零' . $sectionStr;
                    }
                    $sectionStr = $digits[$digit] . $units[$i] . $sectionStr;
                    $hasOutput = true;
                } else {
                    $hasOutput = false;
                }
                $section = (int)($section / 10);
            }

            if ($sectionStr !== '') {
                $result = $sectionStr . $sections[$sectionIndex] . $result;
            }

            $number = (int)($number / 10000);
            $sectionIndex++;
        }

        $result = rtrim($result, '零');

        return ($negative ? '负' : '') . $result;
    }

    /**
     * 字节单位转换
     *
     * @param int $bytes 字节数
     * @param int $precision 精度
     * @return string
     */
    public static function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $i = 0;
        $bytes = max($bytes, 0);

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
