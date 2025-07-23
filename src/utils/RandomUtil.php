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

class RandomUtil
{
    /**
     * 用于随机选的数字
     */
    public static string $BASE_NUMBER = "0123456789";
	/**
     * 用于随机选的字符大写
     */
	public static string $BASE_CHAR_UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    /**
     * 用于随机选的字符小写
     */
	public static string $BASE_CHAR_LOWER = "abcdefghijklmnopqrstuvwxyz";


    /**
     * 生成随机整数 (包含边界)
     * @param int $min
     * @param int $max
     * @return int
     * @throws \Random\RandomException
     * @date 2025/7/23 下午1:45
     * @author 原点 467490186@qq.com
     */
    public static function randomInt(int $min = 0, int $max = PHP_INT_MAX): int
    {
        return random_int($min, $max);
    }

    /**
     * 生成随机浮点数 (0.0 <= n < 1.0)
     * @return float
     * @date 2025/7/23 下午1:45
     * @author 原点 467490186@qq.com
     */
    public static function randomFloat(): float
    {
        return mt_rand() / mt_getrandmax();
    }

    /**
     * 生成指定范围的随机浮点数
     * @param float $min
     * @param float $max
     * @return float
     * @date 2025/7/23 下午1:45
     * @author 原点 467490186@qq.com
     */
    public static function randomFloatBetween(float $min, float $max): float
    {
        return $min + self::randomFloat() * ($max - $min);
    }

    /**
     * 生成随机布尔值
     * @return bool
     * @date 2025/7/23 下午1:47
     * @author 原点 467490186@qq.com
     */
    public static function randomBool(): bool
    {
        return mt_rand(0, 1) === 1;
    }

    /**
     * 生成随机字符串 (默认包含数字和大小写字母)
     * @param int $length
     * @param string|null $chars
     * @return string
     * @date 2025/7/23 下午1:47
     * @author 原点 467490186@qq.com
     */
    public static function randomString(int $length, string $chars = null): string
    {
        $chars = $chars ?? self::$BASE_NUMBER.self::$BASE_CHAR_UPPER.self::$BASE_CHAR_LOWER;
        $result = '';
        $charCount = strlen($chars);

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[mt_rand(0, $charCount - 1)];
        }
        return $result;
    }

    /**
     * 生成随机数字字符串
     * @param int $length
     * @return string
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomNumbers(int $length): string
    {
        return self::randomString($length, self::$BASE_NUMBER);
    }

    /**
     * 生成随机字母字符串 (大小写混合)
     * @param int $length
     * @return string
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomLetters(int $length): string
    {
        return self::randomString($length, self::$BASE_CHAR_UPPER.self::$BASE_CHAR_LOWER);
    }

    /**
     * 生成随机小写字母字符串
     * @param int $length
     * @return string
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomLowerLetters(int $length): string
    {
        return self::randomString($length, self::$BASE_CHAR_LOWER);
    }

    /**
     * 生成随机大写字母字符串
     * @param int $length
     * @return string
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomUpperLetters(int $length): string
    {
        return self::randomString($length, self::$BASE_CHAR_UPPER);
    }

    /**
     * 生成随机十六进制字符串
     * @param int $length
     * @return string
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomHex(int $length): string
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= dechex(mt_rand(0, 15));
        }
        return $result;
    }

    /**
     * 生成随机UUID (标准格式)
     * @return string
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomUUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * 生成简单UUID (无连字符)
     * @return string
     * @throws \Random\RandomException
     * @date 2025/7/23 下午1:48
     * @author 原点 467490186@qq.com
     */
    public static function randomSimpleUUID(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * 生成随机字节数组
     * @param int $length
     * @return string
     * @throws \Random\RandomException
     * @date 2025/7/23 下午1:49
     * @author 原点 467490186@qq.com
     */
    public static function randomBytes(int $length): string
    {
        return random_bytes($length);
    }

    /**
     * 从数组中随机选择一个元素
     * @param array $array
     * @return mixed|null
     * @date 2025/7/23 下午1:49
     * @author 原点 467490186@qq.com
     */
    public static function randomElement(array $array): mixed
    {
        if (empty($array)) {
            return null;
        }
        return $array[array_rand($array)];
    }

    /**
     * 从数组中随机选择多个元素 (不重复)
     * @param array $array
     * @param int $count
     * @return array
     * @date 2025/7/23 下午1:49
     * @author 原点 467490186@qq.com
     */
    public static function randomElements(array $array, int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        $keys = array_rand($array, min($count, count($array)));

        if (!is_array($keys)) {
            return [$array[$keys]];
        }

        $result = [];
        foreach ($keys as $key) {
            $result[] = $array[$key];
        }
        return $result;
    }

    /**
     * 生成随机颜色十六进制值
     * @return string
     * @date 2025/7/23 下午1:49
     * @author 原点 467490186@qq.com
     */
    public static function randomColor(): string
    {
        return '#' . self::randomHex(6);
    }

    /**
     * 生成随机日期 (指定范围内)
     * @param string $start
     * @param string $end
     * @return \DateTime
     * @date 2025/7/23 下午1:49
     * @author 原点 467490186@qq.com
     */
    public static function randomDate(string $start = '1970-01-01', string $end = 'now'): \DateTime
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);
        $randomTs = mt_rand($startTs, $endTs);
        return (new \DateTime())->setTimestamp($randomTs);
    }
}