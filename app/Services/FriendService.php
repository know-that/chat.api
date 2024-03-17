<?php

namespace App\Services;

use App\Enums\RelationEnum;
use App\Facades\ChatFacade;
use App\Models\Chat\ChatSessionModel;
use App\Models\Friend\FriendModel;
use App\Models\Friend\FriendRequestModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\UserModel;
use App\Services\Chat\ChatSession;
use Illuminate\Support\Collection;
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
     * @return ChatSessionModel
     * @throws Throwable
     */
    public function bind(FriendRequestModel $friendRequest, UserModel $user, UserModel $friend): ChatSessionModel
    {
        $word = $friendRequest->state === 10 ? "同意" : "拒绝";

        DB::beginTransaction();
        try {
            // 创建好友关系
            $this->createFriend($user, $friend);

            // 创建会话消息
            $chatSingles = $this->createChatSingles($user, $friend, $word);

            // 创建会话
            $chatSessions = $this->createChatSessions($chatSingles, $user);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        // 当前用户会话
        return $chatSessions->get('user_chat_session');
    }

    /**
     * 创建好友关系
     *
     * @param UserModel $user
     * @param UserModel $friend
     * @return Collection
     */
    protected function createFriend(UserModel $user, UserModel $friend): Collection
    {
        $userFriend = FriendModel::create([
            'user_id'       => $user->id,
            'friend_type'   => 'user',
            'friend_id'     => $friend->id
        ]);
        $friendFriend = FriendModel::create([
            'user_id'       => $friend->id,
            'friend_type'   => 'user',
            'friend_id'     => $user->id
        ]);

        return collect([
            'user'      => $userFriend,
            'friend'    => $friendFriend
        ]);
    }

    /**
     * 创建会话消息
     *
     * @param UserModel $user
     * @param UserModel $friend
     * @param string $word
     * @return Collection
     */
    protected function createChatSingles(UserModel $user, UserModel $friend, string $word): Collection
    {
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
        $userChatSingle->receiver_user = $user;

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
        $friendChatSingle->receiver_user = $friend;

        return collect([
            'user_chat_single'  => $userChatSingle,
            'friend_chat_single'=> $friendChatSingle
        ]);
    }

    /**
     * 创建
     *
     * @param Collection $chatSingles
     * @param UserModel $user
     * @return Collection
     */
    protected function createChatSessions(Collection $chatSingles, UserModel $user): Collection
    {
        $chatSessions = [];
        foreach ($chatSingles as $chatSingle) {
            $chatSource = (new ChatSession)->payload([
                'user_id'           => $chatSingle->receiver_user_id,
                'source_type'       => RelationEnum::User->getName(),
                'source_id'         => $chatSingle->sender_user_id
            ], [
                'last_chat_type'    => RelationEnum::ChatSingle->getName(),
                'last_chat_id'      => $chatSingle->id
            ]);
            $chatSessionKey = $user->id === $chatSingle->receiver_user_id ? 'user_chat_session' : 'friend_chat_session';
            $chatSessions[$chatSessionKey] = ChatFacade::sendTo($chatSingle->receiver_user, $chatSource);
        }

        return collect($chatSessions);
    }
}
