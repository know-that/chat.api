<?php

namespace App\Models\Message;

use App\Models\BaseModel;
use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSingleModel;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MessageTextModel extends BaseModel
{
    protected $table = 'message_text';

    /**
     * 关联通知
     * @return MorphOne
     */
    public function notice(): MorphOne
    {
        return $this->morphOne(ChatNoticeModel::class, 'message');
    }

    /**
     * 关联单聊
     * @return MorphOne
     */
    public function chatSingle(): MorphOne
    {
        return $this->morphOne(ChatSingleModel::class, 'message');
    }
}
