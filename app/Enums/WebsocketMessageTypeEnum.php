<?php

namespace App\Enums;

/**
 * websocket 消息类型
 */
enum WebsocketMessageTypeEnum: string
{
    use CollectTrait;

    case ChatSession = 'chat_session'; // 会话列表
    case Chat = 'chat'; // 聊天
}
