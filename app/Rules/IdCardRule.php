<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 身份证
 * @package App\Rules
 */
class IdCardRule implements Rule
{
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
            '/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/',
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
        return '身份证号格式不正确！';
    }
}
