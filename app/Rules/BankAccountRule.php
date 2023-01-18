<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 银行卡
 * @package App\Rules
 */
class BankAccountRule implements Rule
{
    /**
     * Store a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $n = 0;
        for ($i = strlen($value); $i >= 1; $i--) {
            $index = $i - 1;
            //偶数位
            if ($i % 2 === 0) {
                $n += $value[$index];
            } else {//奇数位
                $t = $value[$index] * 2;
                if ($t > 9) {
                    $t = (int)($t / 10) + $t % 10;
                }
                $n += $t;
            }
        }
        return ($n % 10) === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return '银行卡格式不正确！';
    }
}
