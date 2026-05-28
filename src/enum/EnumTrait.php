<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | @copyright (c) 原点 All rights reserved.
// +----------------------------------------------------------------------
// | Author: 原点 <467490186@qq.com>
// +----------------------------------------------------------------------
// | Date: 2025/6/27
// +----------------------------------------------------------------------
namespace yuandian\Tools\enum;

trait EnumTrait
{
    public function getCode(): string|int
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return $this->getMessageAttribute()?->getMessage() ?? '未知错误';
    }

    public function getExtra(string $key): mixed
    {
        return $this->getMessageAttribute()?->getExtra($key);
    }

    public function getExtras(): array
    {
        return $this->getMessageAttribute()?->getExtras() ?? [];
    }

    /**
     * 自动映射 getXxx() → getExtra('xxx')
     */
    public function __call(string $name, array $arguments = []): mixed
    {
        if (str_starts_with($name, 'get') && strlen($name) > 3) {
            $key = lcfirst(substr($name, 3));
            $val = $this->getExtra($key);
            if ($val !== null) {
                return $val;
            }
        }
        throw new \BadMethodCallException("Method {$name} does not exist on " . static::class);
    }

    private function getMessageAttribute(): ?EnumMeat
    {
        /**
         * 消息属性缓存
         * 结构: [EnumClass => [caseName => Message|false]]
         * false = 已缓存但无 #[Message]（避免重复反射）
         *
         * @var array<string, array<string, EnumMeat|false>> $metaCache
         */
        static $metaCache = [];
        $class = static::class;
        if (!isset($metaCache[$class])) {
            $metaCache[$class] = self::loadEnumMeatCache($class);
        }
        $cached = $metaCache[$class][$this->name] ?? null;

        return $cached === false ? null : $cached;
    }

    /**
     * 批量加载整个枚举类的 Message 属性
     * 一次 ReflectionEnum 替代 N 次 ReflectionEnumUnitCase
     */
    private static function loadEnumMeatCache(string $enumClass): array
    {
        $ref = new \ReflectionEnum($enumClass);
        $cache = [];

        foreach ($ref->getCases() as $caseRef) {
            $attrs = $caseRef->getAttributes(EnumMeat::class);
            if ($attrs) {
                $cache[$caseRef->getName()] = $attrs[0]?->newInstance() ?? false;
            } else {
                $cache[$caseRef->getName()] = false;
            }
        }

        return $cache;
    }

}