<?php

namespace App\Enums\Model;

use App\Enums\CollectTrait;

/**
 * 消息资源类型
 */
enum MessageSourceTypeEnum: string
{
    case MessageText = 'message_text'; // 文本
    case MessageFile = 'message_file'; // 文件

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::MessageText => '文本',
            self::MessageFile => '文件'
        };
    }
}
