<?php

namespace App\Models\Message;

use App\Models\BaseModel;
use App\Models\Chat\ChatNotice;
use App\Models\Chat\ChatSingle;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MessageText extends BaseModel
{
    protected $table = 'message_text';

    /**
     * 关联通知
     * @return MorphOne
     */
    public function notice(): MorphOne
    {
        return $this->morphOne(ChatNotice::class, 'message');
    }

    /**
     * 关联单聊
     * @return MorphOne
     */
    public function chatSingle(): MorphOne
    {
        return $this->morphOne(ChatSingle::class, 'message');
    }
}
