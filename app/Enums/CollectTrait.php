<?php

namespace App\Enums;

use App\Facades\ToolFacade;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

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

    /**
     * 获取枚举 maps
     *
     * @param bool $isKeySnake 是否将key转化为蛇形
     * @return array
     */
    public static function maps(bool $isKeySnake = true): array
    {
        $cases = self::cases();
        $keys = Arr::pluck($cases, 'name');
        $values = Arr::pluck($cases, 'value');

        if ($isKeySnake) {
            foreach ($keys as &$key) {
                $key = ToolFacade::humpToSnake($key);
            }
        }

        return array_combine($keys, $values);
    }

    /**
     * 获取 key 名
     * @param bool $isKeySnake
     * @return string
     */
    public function getName(bool $isKeySnake = true): string
    {
        return $isKeySnake ? ToolFacade::humpToSnake($this->name) : ToolFacade::snakeToHump($this->name);
    }
}
