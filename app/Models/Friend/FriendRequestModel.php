<?php

namespace App\Models\Friend;

use App\Models\BaseModel;
use App\Models\Chat\ChatNoticeModel;
use App\Models\User\UserModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FriendRequestModel extends BaseModel
{
    protected $table = 'friend_request';

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id')->selectRaw('id, nickname, avatar, gender');
    }

    /**
     * 关联好友
     * @return MorphTo
     */
    public function friend(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'friend_id', 'id')->selectRaw('id, nickname, avatar, gender');
    }
}
