<?php

namespace App\Services\Chat;

use App\Enums\Model\MessageFileTypeEnum;
use App\Enums\Model\MessageTypeEnum;
use App\Enums\RelationEnum;
use App\Exceptions\ResourceException;
use App\Facades\ChatFacade;
use App\Models\Chat\ChatGroupModel;
use App\Models\Group\GroupChatModel;
use App\Models\Group\GroupChatUserModel;
use App\Models\UploadModel;
use App\Models\User\UserModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChatGroup implements SendSourceFactory
{
    /**
     * 群聊模型
     * @var GroupChatModel
     */
    public GroupChatModel $groupChat;

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
     * 是否为系统消息
     * @var bool
     */
    public bool $isSystem = false;

    /**
     * 设置发送参数
     *
     * @param GroupChatModel $groupChat
     * @param string         $message
     * @param string         $messageType
     * @param bool           $isSystem
     * @return $this
     */
    public function payload(GroupChatModel $groupChat, string $message, string $messageType, bool $isSystem = false): static
    {
        $this->groupChat = $groupChat;
        $this->message = $message;
        $this->messageType = MessageTypeEnum::tryFrom($messageType);
        $this->isSystem = $isSystem;
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
            $upload = UploadModel::query()->findOrNew($this->message);
            if (!$upload->exists()) {
                throw new ResourceException();
            }

            $messageData = [
                'file_id' => $upload->id,
                'type'    => MessageFileTypeEnum::getTypeBySuffix($upload->suffix)
            ];
        } else {
            $messageData = ['content' => $this->message];
        }

        DB::beginTransaction();
        try {
            // 创建消息
            $messageText = $messageModel->create($messageData);

            // 创建群聊消息
            foreach ($this->groupChat->users as $groupChatUser) {
                $chatGroup = ChatGroupModel::create([
                    'group_chat_id'    => $this->groupChat->id,
                    'receiver_user_id' => $groupChatUser->user_id,
                    'message_type'     => $this->messageType->value,
                    'message_id'       => $messageText->id,
                    'is_system'        => (int)$this->isSystem
                ]);

                // 创建会话
                $this->createSession($this->groupChat, $groupChatUser, $chatGroup);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $chatGroup;
    }

    /**
     * 创建群聊会话
     *
     * @param GroupChatModel     $groupChat
     * @param GroupChatUserModel $groupChatUser
     * @param ChatGroupModel     $chatGroup
     * @return void
     */
    protected function createSession(GroupChatModel $groupChat, GroupChatUserModel $groupChatUser, ChatGroupModel $chatGroup): void
    {
        $chatSource = (new ChatSession)->payload([
            'user_id'     => $groupChatUser->user_id,
            'source_type' => RelationEnum::GroupChat->getName(),
            'source_id'   => $groupChat->id
        ], [
            'last_chat_type' => RelationEnum::ChatGroup->getName(),
            'last_chat_id'   => $chatGroup->id
        ]);
        ChatFacade::sendTo($groupChatUser->user, $chatSource);
    }
}
