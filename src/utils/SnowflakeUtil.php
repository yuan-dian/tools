<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/6/26
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\utils;

/**
 * 雪花算法
 * workerId默认使用本机IP地址
 */
class SnowflakeUtil
{
    /**
     * 时间起始标记点，作为基准，一般取系统的最近时间（一旦确定不能变动）
     * @var int
     */
    private static int $epoch = 1288834974657;

    // 机器ID(0-31)
    private static ?int $datacenterId = null;

    // 工作进程ID(0-31)
    private static ?int $workerId = null;

    // 毫秒内数列(0-4095)
    private static int $sequence = 0;
    // 上传生成ID的时间戳
    private static int $lastTimestamp = -1;
    private static ?string $localIP = null;

    /**
     * 设置时间起始标记点
     * @param int $epoch
     * @date 2025/10/9 下午5:21
     * @author 原点 467490186@qq.com
     */
    public static function setEpoch(int $epoch): void
    {
        self::$epoch = $epoch;
    }

    protected static function getDatacenterId(): int
    {
        if (self::$datacenterId === null) {
            $ipNum = ip2long(self::getLocalIP());
            self::$datacenterId = $ipNum % 31;
        }
        return self::$datacenterId;
    }

    protected static function getWorkerId(): int
    {
        if (self::$workerId=== null) {
            $pid = getmypid() ?: 0;
            self::$workerId = $pid % 31;
        }
        return self::$workerId;
    }

    public static function nextId(): int
    {
        $timestamp = DateUtil::nowTimeMillis();

        if ($timestamp == static::$lastTimestamp) {
            static::$sequence = (static::$sequence + 1) & 4095; // 序列号循环使用，最大4095
            if (static::$sequence == 0) {
                // 序列号溢出，等待下一毫秒
                $timestamp = static::tilNextMillis(static::$lastTimestamp);
            }
        } else {
            static::$sequence = 0; // 重置序列号
        }

        static::$lastTimestamp = $timestamp;

        return (($timestamp - static::$epoch) << 22)
            | (static::getDatacenterId() << 17)
            | static::getWorkerId() << 5
            | static::$sequence;
    }

    private static function tilNextMillis($lastTimestamp): int
    {
        $timestamp = DateUtil::nowTimeMillis();
        while ($timestamp <= $lastTimestamp) {
            usleep(100);
            $timestamp = DateUtil::nowTimeMillis();
        }
        return $timestamp;
    }

    /**
     * 获取本机IP
     * @return string
     * @date 2025/8/15 下午2:38
     * @author 原点 467490186@qq.com
     */
    public static function getLocalIP():string
    {
        if (self::$localIP === null) {
            try {
                $hostName = gethostname();
                $serverIP = gethostbyname($hostName);
            }catch (\Throwable $exception){
                $serverIP = '127.0.0.1';
            }
            // 判断是否是真实IP地址
            if (!filter_var($serverIP, FILTER_VALIDATE_IP)) {
                $serverIP = '127.0.0.1';
            }
            self::$localIP = $serverIP;
        }

        return self::$localIP;
    }
}