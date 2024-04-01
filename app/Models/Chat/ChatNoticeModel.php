<?php

namespace App\Models\Chat;

use App\Enums\RelationEnum;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatNoticeModel extends BaseModel
{
    protected $table = 'chat_notice';

    protected $casts = [
        'user_id'   => 'string'
    ];

    /**
     * 关联资源
     * @return MorphTo
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 关联消息
     * @return MorphTo
     */
    public function message(): MorphTo
    {
        return $this->morphTo();
    }
}
