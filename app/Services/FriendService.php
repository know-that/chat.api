<?php

namespace App\Services;

use App\Enums\RelationEnum;
use App\Models\Chat\ChatSessionModel;
use App\Models\Friend\FriendModel;
use App\Models\Friend\FriendRequestModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\UserModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class FriendService
{
    /**
     * 绑定好友关系
     *
     * @param FriendRequestModel $friendRequest
     * @param UserModel $user
     * @param UserModel $friend
     * @return bool
     * @throws Throwable
     */
    public function bind(FriendRequestModel $friendRequest, UserModel $user, UserModel $friend): bool
    {
        $word = $friendRequest->state === 10 ? "同意" : "拒绝";

        DB::beginTransaction();
        try {
            // 创建好友关系
            FriendModel::create([
                'user_id'       => $user->id,
                'friend_type'   => 'user',
                'friend_id'     => $friend->id
            ]);
            FriendModel::create([
                'user_id'       => $friend->id,
                'friend_type'   => 'user',
                'friend_id'     => $user->id
            ]);

            // 创建会话消息
            $userMessage = MessageTextModel::create([
                'content'     => "{$friend->nickname} {$word}了你的请求"
            ]);
            $userChatSingle = $userMessage->chatSingle()->create([
                'receiver_user_id'  => $user->id,
                'sender_user_id'    => $friend->id,
                'message_type'      => RelationEnum::MessageText->getName(),
                'message_id'        => $userMessage->id,
                'is_system'         => 1
            ]);

            $friendMessage = MessageTextModel::create([
                'content'     => "你已添加用户 {$user->nickname}"
            ]);
            $friendChatSingle = $friendMessage->chatSingle()->create([
                'receiver_user_id'  => $friend->id,
                'sender_user_id'    => $user->id,
                'message_type'      => RelationEnum::MessageText->getName(),
                'message_id'        => $friendMessage->id,
                'is_system'         => 1
            ]);

            // 创建会话
            ChatSessionModel::updateOrCreate([
                'user_id'           => $user->id,
                'source_type'       => RelationEnum::User->getName(),
                'source_id'         => $friend->id
            ], [
                'last_chat_type'    => RelationEnum::ChatSingle->getName(),
                'last_chat_id'      => $userChatSingle->id
            ]);
            ChatSessionModel::updateOrCreate([
                'user_id'           => $friend->id,
                'source_type'       => RelationEnum::User->getName(),
                'source_id'         => $user->id
            ], [
                'last_chat_type'    => RelationEnum::ChatSingle->getName(),
                'last_chat_id'      => $friendChatSingle->id
            ]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return true;
    }
}
