<?php

namespace App\Websocket\Requests\Friend;

use App\Websocket\Requests\BaseRequest;
use JetBrains\PhpStorm\ArrayShape;

class FriendAliasRequest extends BaseRequest
{
    /**
     * 验证字段
     * @return string[]
     */
    #[ArrayShape([])]
    public function rules(): array
    {
        return [
            'alias' => 'required|between:1,50',
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
            'alias' => '备注'
        ];
    }
}
