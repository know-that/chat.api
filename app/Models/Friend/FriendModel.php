<?php

namespace App\Models\Friend;

use App\Models\BaseModel;
use App\Models\User\UserModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FriendModel extends BaseModel
{
    protected $table = 'friend';

    /**
     * 关联用户表
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'friend_id', 'id');
    }
}
