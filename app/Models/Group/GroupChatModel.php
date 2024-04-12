<?php

namespace App\Models\Group;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupChatModel extends BaseModel
{
    protected $table = 'group_chat';

    public $incrementing = false;

    protected $casts = [
        'id'    => 'string'
    ];


    /**
     * 初始化
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        // 设置主键非自增而是雪花编号
        self::snowflakeId();
    }

    /**
     * 群聊成员
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(GroupChatUserModel::class, 'group_chat_id', 'id');
    }
}
