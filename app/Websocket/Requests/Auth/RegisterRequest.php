<?php

namespace App\Websocket\Requests\Auth;

use App\Websocket\Requests\BaseRequest;
use JetBrains\PhpStorm\ArrayShape;

class RegisterRequest extends BaseRequest
{
    /**
     * 验证字段
     * @return string[]
     */
    #[ArrayShape([])]
    public function rules(): array
    {
        return [
            'account'       => 'required|string|between:3,30',
            'password'      => 'required|string|between:3,30'
        ];
    }

    /**
     * 字段别名
     * @return string[]
     */
    #[ArrayShape([])]
    public function attributes(): array
    {
        return [
            'account'       => '账号',
            'password'      => '密码'
        ];
    }
}
