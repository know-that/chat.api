<?php

namespace App\Services\Chat;

use App\Enums\Model\MessageFileTypeEnum;
use App\Enums\Model\MessageTypeEnum;
use App\Enums\RelationEnum;
use App\Exceptions\ResourceException;
use App\Facades\ChatFacade;
use App\Facades\WebsocketFacade;
use App\Models\Chat\ChatSingleModel as ChatSingleModel;
use App\Models\Upload;
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
     * 消息内容
     * @var string
     */
    public string $message;

    /**
     * 消息类型
     * @var MessageTypeEnum
     */
    public MessageTypeEnum $messageType;

    /**
     * 设置发送参数
     *
     * @param UserModel $receiverUser
     * @param string    $message
     * @param string    $messageType
     * @return $this
     */
    public function payload(UserModel $receiverUser, string $message, string $messageType): static
    {
        $this->receiverUser = $receiverUser;
        $this->message = $message;
        $this->messageType = MessageTypeEnum::tryFrom($messageType);
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
        $messageModelClass = $this->messageType->relation();
        $messageModel = new $messageModelClass;

        // 获取消息内容
        if ($this->messageType === MessageTypeEnum::File) {
            // 获取文件类型
            $upload = Upload::query()->findOrNew($this->message);
            if (!$upload->exists()) {
                throw new ResourceException();
            }

            $messageData = [
                'file_id'   => $upload->id,
                'type'      => MessageFileTypeEnum::getTypeBySuffix($upload->suffix)
            ];
        } else {
            $messageData = ['content'   => $this->message];
        }

        DB::beginTransaction();
        try {
            // 创建消息
            $messageText = $messageModel->create($messageData);

            // 创建单聊
            $chatSingle = ChatSingleModel::create([
                'receiver_user_id'  => $user->id,
                'sender_user_id'    => $this->receiverUser->id,
                'message_type'      => $this->messageType->value,
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
