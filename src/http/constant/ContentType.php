<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2026/5/19
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace yuandian\Tools\http\constant;

/**
 * Content-Type 常量池
 */
final class ContentType
{
    // ========================= JSON =========================
    public const JSON = 'application/json';
    public const JSON_UTF8 = 'application/json; charset=utf-8';

    // ========================= 表单 =========================
    public const FORM = 'application/x-www-form-urlencoded';
    public const MULTIPART = 'multipart/form-data';

    // ========================= XML =========================
    public const XML = 'application/xml';
    public const XML_UTF8 = 'application/xml; charset=utf-8';
    public const TEXT_XML = 'text/xml';
    public const SOAP_XML = 'application/soap+xml';

    // ========================= 文本 =========================
    public const TEXT = 'text/plain';
    public const TEXT_UTF8 = 'text/plain; charset=utf-8';
    public const HTML = 'text/html';
    public const HTML_UTF8 = 'text/html; charset=utf-8';
    public const CSS = 'text/css';
    public const CSV = 'text/csv';
    public const JAVASCRIPT = 'application/javascript';

    // ========================= 二进制/流 =========================
    public const OCTET_STREAM = 'application/octet-stream';
    public const PDF = 'application/pdf';
    public const ZIP = 'application/zip';
    public const GZIP = 'application/gzip';
    public const TAR = 'application/x-tar';

    // ========================= 图片 =========================
    public const IMAGE_PNG = 'image/png';
    public const IMAGE_JPEG = 'image/jpeg';
    public const IMAGE_GIF = 'image/gif';
    public const IMAGE_WEBP = 'image/webp';
    public const IMAGE_SVG = 'image/svg+xml';
    public const IMAGE_ICO = 'image/x-icon';

    // ========================= 音视频 =========================
    public const VIDEO_MP4 = 'video/mp4';
    public const AUDIO_MPEG = 'audio/mpeg';
    public const AUDIO_OGG = 'audio/ogg';

    // ========================= 其他 =========================
    public const GRPC = 'application/grpc';
    public const WEBPUSH = 'application/webpush-options+json';
    public const EVENT_STREAM = 'text/event-stream';
    public const FORM_DATA = 'multipart/form-data'; // 别名

    // ========================= 文件扩展名 → MIME 映射 =========================
    private const EXTENSION_MAP = [
        'json' => self::JSON,
        'xml'  => self::XML,
        'html' => self::HTML,
        'htm'  => self::HTML,
        'css'  => self::CSS,
        'js'   => self::JAVASCRIPT,
        'txt'  => self::TEXT,
        'csv'  => self::CSV,
        'pdf'  => self::PDF,
        'zip'  => self::ZIP,
        'gz'   => self::GZIP,
        'tar'  => self::TAR,
        'png'  => self::IMAGE_PNG,
        'jpg'  => self::IMAGE_JPEG,
        'jpeg' => self::IMAGE_JPEG,
        'gif'  => self::IMAGE_GIF,
        'webp' => self::IMAGE_WEBP,
        'svg'  => self::IMAGE_SVG,
        'ico'  => self::IMAGE_ICO,
        'mp4'  => self::VIDEO_MP4,
        'mp3'  => self::AUDIO_MPEG,
        'ogg'  => self::AUDIO_OGG,
    ];

    private function __construct()
    {
    }

    /**
     * 根据文件扩展名获取 MIME
     */
    public static function fromExtension(string $ext): string
    {
        return self::EXTENSION_MAP[strtolower($ext)] ?? self::OCTET_STREAM;
    }

    /**
     * 判断是否为 JSON 类型
     */
    public static function isJson(string $contentType): bool
    {
        return str_contains($contentType, 'json');
    }

    /**
     * 判断是否为 XML 类型
     */
    public static function isXml(string $contentType): bool
    {
        return str_contains($contentType, 'xml');
    }

    /**
     * 判断是否为文本类型
     */
    public static function isText(string $contentType): bool
    {
        return str_starts_with($contentType, 'text/') || str_contains($contentType, 'json') || str_contains(
                $contentType,
                'xml'
            );
    }

    /**
     * 判断是否为二进制类型
     */
    public static function isBinary(string $contentType): bool
    {
        return str_starts_with($contentType, 'application/octet-stream')
            || str_starts_with($contentType, 'image/')
            || str_starts_with($contentType, 'video/')
            || str_starts_with($contentType, 'audio/')
            || in_array($contentType, [self::PDF, self::ZIP, self::GZIP, self::TAR]);
    }

    /**
     * 判断是否为 multipart
     */
    public static function isMultipart(string $contentType): bool
    {
        return str_starts_with($contentType, 'multipart/');
    }
}