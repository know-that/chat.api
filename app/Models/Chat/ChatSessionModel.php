<?php

namespace App\Models\Chat;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatSessionModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'chat_session';

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
     * 关联资源
     * @return MorphTo
     */
    public function lastChat(): MorphTo
    {
        return $this->morphTo();

    }
}
