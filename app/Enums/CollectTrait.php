<?php

namespace App\Enums;

use Illuminate\Support\Collection;

trait CollectTrait
{
    /**
     * cases 集合
     * @return Collection
     */
    public static function collect(): Collection
    {
        return Collection::make(self::cases());
    }

    /**
     * 获取 value 数组
     * @return array
     */
    public static function values(): array
    {
        return self::collect()->pluck('value')->toArray();
    }
}
