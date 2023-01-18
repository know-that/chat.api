<?php

namespace App\Models\Friend;

use App\Models\BaseModel;
use App\Models\Chat\ChatSession;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FriendRequest extends BaseModel
{
    protected $table = 'friend_request';
}
