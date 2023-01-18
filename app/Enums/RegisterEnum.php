<?php

namespace App\Enums;

/**
 * 注册方式
 */
enum RegisterEnum: string
{
    use CollectTrait;

    case Email = 'register'; // 邮箱

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::Email => '邮箱'
        };
    }
}
