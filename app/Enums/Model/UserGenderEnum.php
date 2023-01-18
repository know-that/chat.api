<?php

namespace App\Enums\Model;

use App\Enums\CollectTrait;

/**
 * 用户性别
 */
enum UserGenderEnum: int
{
    use CollectTrait;

    case Man = 1; // 男
    case Woman = 0; // 女

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::Man => '男',
            self::Woman => '女'
        };
    }
}
