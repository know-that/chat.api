<?php

namespace App\Models\Chat;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatGroupModel extends BaseModel
{
    protected $table = 'chat_group';

    protected $casts = [
        'group_chat_id'   => 'string',
        'sender_user_id'   => 'string'
    ];

    /**
     * 关联消息
     * @return MorphTo
     */
    public function message(): MorphTo
    {
        return $this->morphTo();
    }
}
