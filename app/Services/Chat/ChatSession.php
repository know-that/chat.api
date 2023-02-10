<?php

namespace App\Services\Chat;

use App\Enums\WebsocketMessageTypeEnum;
use App\Facades\WebsocketFacade;
use App\Models\Chat\ChatSessionModel;
use App\Models\User\UserModel;

class ChatSession implements SendSourceFactory
{
    /**
     * 会话对象资源
     * @var array
     */
    public array $source;

    /**
     * 最后聊天信息
     * @var array
     */
    public array $lastChat;

    public function payload(array $source, array $lastChat): static
    {
        $this->source = $source;
        $this->lastChat = $lastChat;
        return $this;
    }

    /**
     * 创建会话
     *
     * @param UserModel $user
     * @return ChatSession
     */
    public function create(UserModel $user): ChatSession
    {
        $source = $this->source;
        $source['user_id'] = $user->id;

        // 创建发送方会话
        $chatSession = ChatSessionModel::updateOrcreate($source, $this->lastChat);

        // 发送会话消息
        WebsocketFacade::insideSend($user, $chatSession, WebsocketMessageTypeEnum::ChatSession);

        return $chatSession;
    }
}
