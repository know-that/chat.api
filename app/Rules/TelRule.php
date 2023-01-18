<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 手机号
 * @package App\Rules
 */
class TelRule implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match(
            '/^[1](([3][0-9])|([4][5-9])|([5][0-3,5-9])|([6][5,6])|([7][0-8])|([8][0-9])|([9][1,8,9]))[0-9]{8}$/',
            $value
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return '手机号格式不正确！';
    }
}
