<?php

namespace App\Models\Chat;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatSingleModel extends BaseModel
{
    protected $table = 'chat_single';

    protected $casts = [
        'receiver_user_id'   => 'string',
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
