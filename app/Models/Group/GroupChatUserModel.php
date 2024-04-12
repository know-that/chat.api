<?php

namespace App\Models\Group;

use App\Models\BaseModel;
use App\Models\User\UserModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupChatUserModel extends BaseModel
{
    protected $table = 'group_chat_user';

    protected $casts = [
        'group_chat_id' => 'string',
        'user_id'       => 'string'
    ];

    /**
     * 用户
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}
