<?php

namespace App\Enums\Model;

use App\Enums\CollectTrait;
use App\Models\Message\MessageFileModel;
use App\Models\Message\MessageTextModel;

/**
 * 消息类型
 */
enum MessageTypeEnum: string
{
    use CollectTrait;

    case Text = 'message_text';
    case File = 'message_file';

    /**
     * 获取当前模型关联关系
     * @return string
     */
    public function relation(): string
    {
        return match ($this) {
            self::Text => MessageTextModel::class,
            self::File => MessageFileModel::class
        };
    }
}
