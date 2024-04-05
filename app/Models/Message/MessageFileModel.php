<?php

namespace App\Models\Message;

use App\Enums\Model\MessageFileTypeEnum;
use App\Models\BaseModel;
use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSingleModel;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MessageFileModel extends BaseModel
{
    protected $table = 'message_file';

    protected $appends = [
        'type_text'
    ];

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

    /**
     * 关联上传文件
     * @return BelongsTo
     */
    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'file_id');
    }

    /**
     * gender_text 获取/访问器
     * @return Attribute
     */
    public function typeText(): Attribute
    {
        return new Attribute(
            get: fn ($value) => MessageFileTypeEnum::tryFrom($this->type)?->text() ?? ''
        );
    }
}
