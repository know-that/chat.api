<?php

namespace App\Services\Chat;

use App\Enums\RelationEnum;
use App\Facades\ChatFacade;
use App\Facades\WebsocketFacade;
use App\Models\Chat\ChatSingleModel as ChatSingleModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\UserModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChatSingle implements SendSourceFactory
{
    /**
     * 接收方
     * @var UserModel
     */
    public UserModel $receiverUser;

    /**
     * 消息类型
     * @var string
     */
    public string $message;

    /**
     * 设置发送参数
     *
     * @param UserModel $receiverUser
     * @param string $message
     * @return $this
     */
    public function payload(UserModel $receiverUser, string $message): static
    {
        $this->receiverUser = $receiverUser;
        $this->message = $message;
        return $this;
    }

    /**
     * 创建消息
     *
     * @param UserModel $user 发送方
     * @return mixed
     * @throws Throwable
     */
    public function create(UserModel $user): mixed
    {
        DB::beginTransaction();
        try {
            // 创建消息
            $messageText = MessageTextModel::create([
                'content'     => $this->message
            ]);

            // 创建单聊
            $chatSingle = ChatSingleModel::create([
                'receiver_user_id'  => $user->id,
                'sender_user_id'    => $this->receiverUser->id,
                'message_type'      => RelationEnum::MessageText->getName(),
                'message_id'        => $messageText->id
            ]);

            // 创建发送方会话
            $this->createBySelf($user, $chatSingle);

            // 创建接收方会话
            $this->createByReceiver($user, $chatSingle);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $chatSingle->load('message');

        // 发送消息
        WebsocketFacade::send($this->receiverUser, $chatSingle->toJson());

        return $chatSingle;
    }

    /**
     * 创建发送方会话
     *
     * @param UserModel $user
     * @param ChatSingleModel $chatSingle
     * @return void
     */
    protected function createBySelf(UserModel $user, ChatSingleModel $chatSingle): void
    {
        $chatSource = (new ChatSession)->payload([
            'user_id'       => $user->id,
            'source_type'   => RelationEnum::User->getName(),
            'source_id'     => $this->receiverUser->id
        ],[
            'last_chat_type' => RelationEnum::ChatSingle->getName(),
            'last_chat_id'   => $chatSingle->id
        ]);
        ChatFacade::sendTo($user, $chatSource);
    }

    /**
     * 创建接收方会话
     *
     * @param UserModel $user
     * @param ChatSingleModel $chatSingle
     * @return void
     */
    protected function createByReceiver(UserModel $user, ChatSingleModel $chatSingle): void
    {
        $chatSource = (new ChatSession)->payload([
            'user_id'       => $this->receiverUser->id,
            'source_type'   => RelationEnum::User->getName(),
            'source_id'     => $user->id
        ],[
            'last_chat_type' => RelationEnum::ChatSingle->getName(),
            'last_chat_id'   => $chatSingle->id
        ]);
        ChatFacade::sendTo($this->receiverUser, $chatSource);
    }
}
