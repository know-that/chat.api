<?php

namespace App\Models\Chat;

use App\Models\BaseModel;
use App\Models\User\UserModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * 关联发信者
     * @return BelongsTo
     */
    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'sender_user_id', 'id');
    }
}
