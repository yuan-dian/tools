<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/8/14
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;

use DateTimeImmutable;
use DateTimeInterface;
use yuandian\Tools\const\DateFormatConst;

class DateUtil
{

    /**
     * 获取当前时间对象
     * @return DateTimeImmutable
     * @date 2025/8/14 下午4:38
     * @author 原点 467490186@qq.com
     */
    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    /**
     * 获取秒级时间戳
     * @return int
     * @date 2025/8/14 上午11:37
     * @author 原点 467490186@qq.com
     */
    public static function nowTime(): int
    {
        return time();
    }

    /**
     * 获取毫秒时间戳
     * @return int
     * @date 2025/8/14 上午11:37
     * @author 原点 467490186@qq.com
     */
    public static function nowTimeMillis(): int
    {
        $now = microtime(true);
        return (int)($now * 1000);
    }

    /**
     * 格式化时间为字符串
     * @param DateTimeInterface $date 时间对象
     * @param string $format 格式(默认Y-m-d H:i:s)
     * @return string
     */
    public static function format(DateTimeInterface $date, string $format = DateFormatConst::NORM_DATE_TIME_PATTERN): string
    {
        return $date->format($format);
    }

    /**
     * 字符串转时间对象
     * @param string $dateStr 时间字符串
     * @param string $format 输入格式(默认Y-m-d H:i:s)
     */
    public static function parse(string $dateStr, string $format = DateFormatConst::NORM_DATE_TIME_PATTERN): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat($format, $dateStr);
    }

    /**
     * 时间戳转时间对象
     */
    public static function parseInt(?int $time = null, bool $millis = false): DateTimeImmutable
    {
        if (empty($time)) {
            $time = self::nowTime();
        }
        $seconds = $millis ? (int)($time / 1000) : $time;
        $microseconds = $millis ? (($time % 1000) * 1000) : 0;
        return DateTimeImmutable::createFromFormat(
            'U u',
            sprintf('%d %06d', $seconds, $microseconds)
        );
    }

    /**
     * 时间偏移计算
     * @param DateTimeInterface $date 基准时间
     * @param string $modify 偏移量表达式(如"+1 day")
     */
    public static function offset(DateTimeInterface $date, string $modify): DateTimeImmutable
    {
        $immutable = $date instanceof DateTimeImmutable ? $date : DateTimeImmutable::createFromMutable($date);
        return $immutable->modify($modify);
    }

    /**
     * 计算两个时间的差值(秒)
     */
    public static function diffSeconds(DateTimeInterface $date1, DateTimeInterface $date2): int
    {
        return abs($date1->getTimestamp() - $date2->getTimestamp());
    }

    /**
     * 获取某天的开始时间(00:00:00)
     */
    public static function beginOfDay(DateTimeInterface $date): DateTimeImmutable
    {
        return self::parse($date->format(DateFormatConst::NORM_DATE_PATTERN) . ' 00:00:00');
    }

    /**
     * 获取某天的结束时间(23:59:59)
     */
    public static function endOfDay(DateTimeInterface $date): DateTimeImmutable
    {
        return self::parse($date->format(DateFormatConst::NORM_DATE_PATTERN) . ' 23:59:59');
    }

    /**
     * 判断是否为闰年
     */
    public static function isLeapYear(DateTimeInterface $date): bool
    {
        return $date->format('L') === '1';
    }

    /**
     * 获取年
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:38
     * @author 原点 467490186@qq.com
     */
    public static function year(DateTimeInterface $date): int
    {
        return (int)$date->format('Y');
    }

    /**
     * 获取月
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:38
     * @author 原点 467490186@qq.com
     */
    public static function month(DateTimeInterface $date): int
    {
        return (int)$date->format('m');
    }

    /**
     * 获取天
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:38
     * @author 原点 467490186@qq.com
     */
    public static function day(DateTimeInterface $date): int
    {
        return (int)$date->format('d');
    }

    /**
     * 获取小时
     * @param DateTimeInterface $date
     * @param bool $is24HourClock
     * @return int
     * @date 2025/8/14 下午4:39
     * @author 原点 467490186@qq.com
     */
    public static function hour(DateTimeInterface $date, bool $is24HourClock = true): int
    {
        return (int)$date->format($is24HourClock ? 'H' : 'h');
    }

    /**
     * 获取分钟
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:39
     * @author 原点 467490186@qq.com
     */
    public static function minute(DateTimeInterface $date): int
    {
        return (int)$date->format('i');
    }

    /**
     * 获取秒
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:39
     * @author 原点 467490186@qq.com
     */
    public static function seconds(DateTimeInterface $date): int
    {
        return (int)$date->format('s');
    }

    /**
     * 获取毫秒
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:39
     * @author 原点 467490186@qq.com
     */
    public static function milliseconds(DateTimeInterface $date): int
    {
        return (int)$date->format('v');
    }

    /**
     * 获取微妙
     * @param DateTimeInterface $date
     * @return int
     * @date 2025/8/14 下午4:39
     * @author 原点 467490186@qq.com
     */
    public static function microseconds(DateTimeInterface $date): int
    {
        return (int)$date->format('u');
    }

}