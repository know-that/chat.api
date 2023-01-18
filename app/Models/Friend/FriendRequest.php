<?php

namespace App\Models\Friend;

use App\Models\BaseModel;
use App\Models\Chat\ChatSession;
use App\Models\Notice;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class FriendRequest extends BaseModel
{
    protected $table = 'friend_request';

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->selectRaw('id, nickname, avatar, gender');
    }

    /**
     * 关联好友
     * @return MorphTo
     */
    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id', 'id')->selectRaw('id, nickname, avatar, gender');
    }

    /**
     * 关联通知
     * @return MorphMany
     */
    public function notices(): MorphMany
    {
        return $this->morphMany(Notice::class, 'source');
    }
}
