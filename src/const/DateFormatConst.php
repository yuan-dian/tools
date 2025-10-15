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

namespace yuandian\Tools\const;

interface DateFormatConst
{
    public const  NORM_DATE_PATTERN = "Y-m-d";
    public const  NORM_DATE_TIME_PATTERN = "Y-m-d H:i:s";
    public const  NORM_TIME_PATTERN = "H:i:s";
    public const  NORM_DATETIME_MINUTE_PATTERN = "Y-m-d H:i";
    public const  NORM_DATETIME_PATTERN = "Y-m-d H:i:s";
    public const  NORM_DATETIME_MS_PATTERN = "Y-m-d H:i:s.v";
    public const  CHINESE_DATE_PATTERN = "Y年m月d日";
    public const  PURE_DATE_PATTERN = "Ymd";
    public const  PURE_TIME_PATTERN = "His";
    public const  PURE_DATETIME_PATTERN = "YmdHis";

    /**
     * @since 7.2
     */
    public const  ATOM = 'Y-m-d\TH:i:sP';

    /**
     * @since 7.2
     */
    public const  COOKIE = 'l, d-M-Y H:i:s T';

    /**
     * This format is not compatible with ISO-8601, but is left this way for backward compatibility reasons.
     * Use DateTime::ATOM or DATE_ATOM for compatibility with ISO-8601 instead.
     * @since 7.2
     * @deprecated
     */
    public const  ISO8601 = 'Y-m-d\TH:i:sO';

    /**
     * @since 8.2
     */
    public const  ISO8601_EXPANDED = 'X-m-d\\TH:i:sP';

    /**
     * @since 7.2
     */
    public const RFC822 = 'D, d M y H:i:s O';

    /**
     * @since 7.2
     */
    public const  RFC850 = 'l, d-M-y H:i:s T';

    /**
     * @since 7.2
     */
    public const  RFC1036 = 'D, d M y H:i:s O';

    /**
     * @since 7.2
     */
    public const  RFC1123 = 'D, d M Y H:i:s O';

    /**
     * @since 7.2
     */
    public const  RFC2822 = 'D, d M Y H:i:s O';

    /**
     * @since 7.2
     */
    public const  RFC3339 = 'Y-m-d\TH:i:sP';

    /**
     * @since 7.2
     */
    public const  RFC3339_EXTENDED = 'Y-m-d\TH:i:s.vP';

    /**
     * @since 7.2
     */
    public const  RFC7231 = 'D, d M Y H:i:s \G\M\T';

    /**
     * @since 7.2
     */
    public const  RSS = 'D, d M Y H:i:s O';

    /**
     * @since 7.2
     */
    public const  W3C = 'Y-m-d\TH:i:sP';
}