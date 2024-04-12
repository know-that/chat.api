<?php

namespace App\Websocket\Requests\GroupChat;

use App\Websocket\Requests\BaseRequest;
use JetBrains\PhpStorm\ArrayShape;

class GroupChatStoreRequest extends BaseRequest
{
    /**
     * 验证字段
     * @return string[]
     */
    #[ArrayShape([])]
    public function rules(): array
    {
        return [
            'friend_ids' => 'required|array',
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
            'friend_ids' => '群成员'
        ];
    }
}
