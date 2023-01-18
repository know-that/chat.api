<?php

namespace App\Models\Chat;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatSession extends BaseModel
{
    use SoftDeletes;

    protected $table = 'chat_session';

    /**
     * 关联资源
     * @return MorphTo
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * 关联资源
     * @return MorphTo
     */
    public function lastMessage(): MorphTo
    {
        return $this->morphTo();
    }
}
