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
    public static function format(
        DateTimeInterface $date,
        string $format = DateFormatConst::NORM_DATE_TIME_PATTERN
    ): string {
        return $date->format($format);
    }

    /**
     * 字符串转时间对象
     * @param string $dateStr 时间字符串
     * @param string $format 输入格式(默认Y-m-d H:i:s)
     */
    public static function parse(
        string $dateStr,
        string $format = DateFormatConst::NORM_DATE_TIME_PATTERN
    ): DateTimeImmutable {
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


    // ==================== 日期加减 ====================

    /**
     * 加减天数
     * @param DateTimeInterface $date
     * @param int $days
     * @return DateTimeImmutable
     */
    public static function addDays(DateTimeInterface $date, int $days): DateTimeImmutable
    {
        return self::offset($date, "{$days} day");
    }

    /**
     * 加减小时
     * @param DateTimeInterface $date
     * @param int $hours
     * @return DateTimeImmutable
     */
    public static function addHours(DateTimeInterface $date, int $hours): DateTimeImmutable
    {
        return self::offset($date, "{$hours} hour");
    }

    /**
     * 加减分钟
     * @param DateTimeInterface $date
     * @param int $minutes
     * @return DateTimeImmutable
     */
    public static function addMinutes(DateTimeInterface $date, int $minutes): DateTimeImmutable
    {
        return self::offset($date, "{$minutes} minute");
    }

    /**
     * 加减秒数
     * @param DateTimeInterface $date
     * @param int $seconds
     * @return DateTimeImmutable
     */
    public static function addSeconds(DateTimeInterface $date, int $seconds): DateTimeImmutable
    {
        return self::offset($date, "{$seconds} second");
    }

    /**
     * 加减月数
     * @param DateTimeInterface $date
     * @param int $months
     * @return DateTimeImmutable
     */
    public static function addMonths(DateTimeInterface $date, int $months): DateTimeImmutable
    {
        return self::offset($date, "{$months} month");
    }

    /**
     * 加减年数
     * @param DateTimeInterface $date
     * @param int $years
     * @return DateTimeImmutable
     */
    public static function addYears(DateTimeInterface $date, int $years): DateTimeImmutable
    {
        return self::offset($date, "{$years} year");
    }

    // ==================== 日期比较 ====================

    /**
     * 是否在指定日期之前
     */
    public static function isBefore(DateTimeInterface $date1, DateTimeInterface $date2): bool
    {
        return $date1 < $date2;
    }

    /**
     * 是否在指定日期之后
     */
    public static function isAfter(DateTimeInterface $date1, DateTimeInterface $date2): bool
    {
        return $date1 > $date2;
    }

    /**
     * 是否在两个日期之间
     */
    public static function isBetween(DateTimeInterface $date, DateTimeInterface $start, DateTimeInterface $end): bool
    {
        return $date >= $start && $date <= $end;
    }

    /**
     * 是否同一天
     */
    public static function isSameDay(DateTimeInterface $date1, DateTimeInterface $date2): bool
    {
        return $date1->format('Y-m-d') === $date2->format('Y-m-d');
    }

    /**
     * 是否同一小时
     */
    public static function isSameHour(DateTimeInterface $date1, DateTimeInterface $date2): bool
    {
        return $date1->format('Y-m-d H') === $date2->format('Y-m-d H');
    }

    /**
     * 是否同一分钟
     */
    public static function isSameMinute(DateTimeInterface $date1, DateTimeInterface $date2): bool
    {
        return $date1->format('Y-m-d H:i') === $date2->format('Y-m-d H:i');
    }

    // ==================== 日期判断 ====================

    /**
     * 是否为今天
     */
    public static function isToday(DateTimeInterface $date): bool
    {
        return self::isSameDay($date, self::now());
    }

    /**
     * 是否为昨天
     */
    public static function isYesterday(DateTimeInterface $date): bool
    {
        return self::isSameDay($date, self::addDays(self::now(), -1));
    }

    /**
     * 是否为明天
     */
    public static function isTomorrow(DateTimeInterface $date): bool
    {
        return self::isSameDay($date, self::addDays(self::now(), 1));
    }

    /**
     * 是否为周末
     */
    public static function isWeekend(DateTimeInterface $date): bool
    {
        $dayOfWeek = (int)$date->format('N');
        return $dayOfWeek >= 6;
    }

    /**
     * 是否为工作日
     */
    public static function isWorkday(DateTimeInterface $date): bool
    {
        return !self::isWeekend($date);
    }

    /**
     * 是否为未来时间
     */
    public static function isFuture(DateTimeInterface $date): bool
    {
        return $date > self::now();
    }

    /**
     * 是否为过去时间
     */
    public static function isPast(DateTimeInterface $date): bool
    {
        return $date < self::now();
    }

    /**
     * 是否为闰年 (按年份)
     */
    public static function isLeapYearByYear(int $year): bool
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }

    // ==================== 获取信息 ====================

    /**
     * 获取星期几 (1=周一, 7=周日)
     */
    public static function getDayOfWeek(DateTimeInterface $date): int
    {
        return (int)$date->format('N');
    }

    /**
     * 获取第几周
     */
    public static function getWeekOfYear(DateTimeInterface $date): int
    {
        return (int)$date->format('W');
    }

    /**
     * 获取第几天
     */
    public static function getDayOfYear(DateTimeInterface $date): int
    {
        return (int)$date->format('z') + 1;
    }

    /**
     * 获取当月天数
     */
    public static function getDaysInMonth(DateTimeInterface $date): int
    {
        return (int)$date->format('t');
    }

    /**
     * 获取当年天数
     */
    public static function getDaysInYear(DateTimeInterface $date): int
    {
        return self::isLeapYear($date) ? 366 : 365;
    }

    /**
     * 获取指定年月的天数
     */
    public static function getDaysInMonthByYearMonth(int $year, int $month): int
    {
        return (int)cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * 获取季度 (1-4)
     */
    public static function getQuarter(DateTimeInterface $date): int
    {
        return (int)ceil((int)$date->format('m') / 3);
    }

    /**
     * 获取时间戳
     */
    public static function toTimestamp(DateTimeInterface $date): int
    {
        return $date->getTimestamp();
    }

    /**
     * 获取毫秒时间戳
     */
    public static function toMillis(DateTimeInterface $date): int
    {
        return $date->getTimestamp() * 1000 + (int)$date->format('v');
    }

    // ==================== 格式化 ====================

    /**
     * 转字符串
     */
    public static function toString(DateTimeInterface $date, string $format = 'Y-m-d H:i:s'): string
    {
        return $date->format($format);
    }

    /**
     * 格式化时间戳
     */
    public static function formatTimestamp(int $timestamp, string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, $timestamp);
    }

    /**
     * 相对时间格式化 (如: 3分钟前, 2小时前, 1天前)
     */
    public static function formatRelative(DateTimeInterface $date): string
    {
        $now = self::now();
        $diff = $now->getTimestamp() - $date->getTimestamp();

        if ($diff < 0) {
            $diff = abs($diff);
            if ($diff < 60) {
                return "{$diff}秒后";
            }
            if ($diff < 3600) {
                return floor($diff / 60) . "分钟后";
            }
            if ($diff < 86400) {
                return floor($diff / 3600) . "小时后";
            }
            if ($diff < 2592000) {
                return floor($diff / 86400) . "天后";
            }
            if ($diff < 31536000) {
                return floor($diff / 2592000) . "个月后";
            }
            return floor($diff / 31536000) . "年后";
        }

        if ($diff < 60) {
            return "刚刚";
        }
        if ($diff < 3600) {
            return floor($diff / 60) . "分钟前";
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . "小时前";
        }
        if ($diff < 2592000) {
            return floor($diff / 86400) . "天前";
        }
        if ($diff < 31536000) {
            return floor($diff / 2592000) . "个月前";
        }
        return floor($diff / 31536000) . "年前";
    }

    /**
     * 中文格式化
     */
    public static function formatChinese(DateTimeInterface $date): string
    {
        return $date->format('Y年m月d日H时i分s秒');
    }

    // ==================== 时间段计算 ====================

    /**
     * 计算天数差
     */
    public static function diffDays(DateTimeInterface $date1, DateTimeInterface $date2): int
    {
        $diff = $date1->diff($date2);
        return (int)$diff->format('%r%a');
    }

    /**
     * 计算月数差
     */
    public static function diffMonths(DateTimeInterface $date1, DateTimeInterface $date2): int
    {
        $diff = $date1->diff($date2);
        return (int)$diff->format('%r%m') + ((int)$diff->format('%r%y') * 12);
    }

    /**
     * 计算年数差
     */
    public static function diffYears(DateTimeInterface $date1, DateTimeInterface $date2): int
    {
        $diff = $date1->diff($date2);
        return (int)$diff->format('%r%y');
    }

    /**
     * 获取时间差对象
     */
    public static function getDiff(DateTimeInterface $date1, DateTimeInterface $date2): \DateInterval
    {
        return $date1->diff($date2);
    }

    /**
     * 获取时间差字符串 (如: 1天2小时3分钟)
     */
    public static function getDiffString(DateTimeInterface $date1, DateTimeInterface $date2): string
    {
        $diff = $date1->diff($date2);
        $parts = [];

        if ($diff->y > 0) {
            $parts[] = $diff->y . '年';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . '月';
        }
        if ($diff->d > 0) {
            $parts[] = $diff->d . '天';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . '小时';
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i . '分钟';
        }
        if ($diff->s > 0) {
            $parts[] = $diff->s . '秒';
        }

        return empty($parts) ? '0秒' : implode('', $parts);
    }

    // ==================== 周期边界 ====================

    /**
     * 获取本周开始 (周一)
     */
    public static function beginOfWeek(DateTimeInterface $date): DateTimeImmutable
    {
        $dayOfWeek = (int)$date->format('N');
        return self::addDays(self::beginOfDay($date), -(($dayOfWeek - 1)));
    }

    /**
     * 获取本周结束 (周日 23:59:59)
     */
    public static function endOfWeek(DateTimeInterface $date): DateTimeImmutable
    {
        return self::endOfDay(self::addDays(self::beginOfWeek($date), 6));
    }

    /**
     * 获取月初开始
     */
    public static function beginOfMonth(DateTimeInterface $date): DateTimeImmutable
    {
        return self::parse($date->format('Y-m') . '-01 00:00:00');
    }

    /**
     * 获取月末结束
     */
    public static function endOfMonth(DateTimeInterface $date): DateTimeImmutable
    {
        return self::endOfDay(self::parse($date->format('Y-m') . '-' . $date->format('t'), 'Y-m-d'));
    }

    /**
     * 获取季初开始
     */
    public static function beginOfQuarter(DateTimeInterface $date): DateTimeImmutable
    {
        $quarter = self::getQuarter($date);
        $month = (($quarter - 1) * 3) + 1;
        return self::parse($date->format('Y') . '-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '-01 00:00:00');
    }

    /**
     * 获取季末结束
     */
    public static function endOfQuarter(DateTimeInterface $date): DateTimeImmutable
    {
        $quarter = self::getQuarter($date);
        $month = $quarter * 3;
        return self::endOfMonth(
            self::parse($date->format('Y') . '-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '-01', 'Y-m-d')
        );
    }

    /**
     * 获取年初开始
     */
    public static function beginOfYear(DateTimeInterface $date): DateTimeImmutable
    {
        return self::parse($date->format('Y') . '-01-01 00:00:00');
    }

    /**
     * 获取年末结束
     */
    public static function endOfYear(DateTimeInterface $date): DateTimeImmutable
    {
        return self::parse($date->format('Y') . '-12-31 23:59:59');
    }

    // ==================== 转换 ====================

    /**
     * DateTimeImmutable 转 DateTime
     */
    public static function toMutable(DateTimeImmutable $date): \DateTime
    {
        return \DateTime::createFromImmutable($date);
    }

    /**
     * DateTime 转 DateTimeImmutable
     */
    public static function toImmutable(DateTimeInterface $date): DateTimeImmutable
    {
        if ($date instanceof DateTimeImmutable) {
            return $date;
        }
        return DateTimeImmutable::createFromMutable($date);
    }

    /**
     * 从时间戳创建
     */
    public static function fromTimestamp(int $timestamp): DateTimeImmutable
    {
        return self::parseInt($timestamp);
    }

    /**
     * 从日期字符串创建 (自动识别格式)
     */
    public static function parseAuto(string $dateStr): ?DateTimeImmutable
    {
        $formats = ['Y-m-d H:i:s', 'Y-m-d', 'Y/m/d', 'Ymd', 'Y-m-d\TH:i:s', 'Y-m-d\TH:i:sP'];
        foreach ($formats as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $dateStr);
            if ($date !== false) {
                return $date;
            }
        }
        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            return self::parseInt($timestamp);
        }
        return null;
    }

    /**
     * 设置时间
     */
    public static function setTime(DateTimeInterface $date, int $hour, int $minute, int $second = 0): DateTimeImmutable
    {
        $immutable = self::toImmutable($date);
        return $immutable->setTime($hour, $minute, $second);
    }

    /**
     * 设置日期
     */
    public static function setDate(DateTimeInterface $date, int $year, int $month, int $day): DateTimeImmutable
    {
        $immutable = self::toImmutable($date);
        return $immutable->setDate($year, $month, $day);
    }

    // ==================== 工具方法 ====================

    /**
     * 计算年龄
     */
    public static function getAge(DateTimeInterface $birthday): int
    {
        $now = self::now();
        $age = (int)$now->format('Y') - (int)$birthday->format('Y');
        if ((int)$now->format('md') < (int)$birthday->format('md')) {
            $age--;
        }
        return $age;
    }

    /**
     * 是否过期
     */
    public static function isExpired(DateTimeInterface $expireTime): bool
    {
        return self::now() > $expireTime;
    }

    /**
     * 获取当月第一天
     */
    public static function firstDayOfMonth(DateTimeInterface $date): int
    {
        return (int)self::beginOfMonth($date)->format('d');
    }

    /**
     * 获取当月最后一天
     */
    public static function lastDayOfMonth(DateTimeInterface $date): int
    {
        return (int)self::endOfMonth($date)->format('d');
    }

}