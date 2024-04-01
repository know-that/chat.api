<?php

namespace App\Services\Chat;

use App\Enums\WebsocketMessageTypeEnum;
use App\Facades\ChatSessionFacade;
use App\Facades\WebsocketFacade;
use App\Models\Chat\ChatSessionModel;
use App\Models\User\UserModel;
use Illuminate\Support\Facades\Cache;

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

    /**
     * 参数设置
     *
     * @param array $source
     * @param array $lastChat
     * @return $this
     */
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
     * @return ChatSessionModel
     */
    public function create(UserModel $user): ChatSessionModel
    {
        $source = $this->source;
        $source['user_id'] = $user->id;

        // 创建发送方会话
        $chatSession = ChatSessionModel::updateOrcreate($source, $this->lastChat);

        // 重新获取 chatSession 详情
        $chatSession->load(ChatSessionFacade::relations($user));

        // 删除会话缓存
        Cache::delete("chat-sessions:{$user->id}");

        // 发送会话更新消息
        WebsocketFacade::send($user, [], WebsocketMessageTypeEnum::ChatSession);

        return $chatSession;
    }
}
