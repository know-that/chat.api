<?php

namespace App\Traits\Tool;

use Exception;
use Illuminate\Support\Str;

trait IterableTrait
{
    /**
     * 移除数组指定单元
     *
     * @param iterable $iterable
     * @param string $key
     * @return void
     */
    public function iterableForget(iterable &$iterable, string $key): void
    {
        foreach ($iterable as $index=>&$item) {
            if ($index === $key) {
                unset($iterable[$key]);
            } else if (is_iterable($item)) {
                $this->iterableForget($item, $key);
            }
        }
    }
}
