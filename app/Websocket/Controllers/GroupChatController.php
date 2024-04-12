<?php

namespace App\Websocket\Controllers;

use App\Enums\Model\GroupChatUserIdentityEnum;
use App\Enums\Model\MessageTypeEnum;
use App\Facades\ChatFacade;
use App\Models\Friend\FriendModel;
use App\Models\Group\GroupChatModel;
use App\Services\Chat\ChatGroup;
use App\Services\Chat\ChatSingle;
use App\Services\ToolService;
use App\Websocket\Requests\GroupChat\GroupChatStoreRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Throwable;

/**
 * 群聊
 */
class GroupChatController extends Controller
{
    /**
     * 创建
     *
     * @param GroupChatStoreRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(GroupChatStoreRequest $request): JsonResponse
    {
        $params = $request->only(['friend_ids']);
        $user = $request->user();
        // 获取好友
        $friends = FriendModel::query()->with('user:id,nickname,avatar')->where('user_id', $user->id)->find($params['friend_ids']);
        $nicknames = Arr::pluck($friends, 'user.nickname');

        DB::beginTransaction();
        try {
            // 创建群聊
            $groupChat = GroupChatModel::query()->create([
                'nickname'   => mb_substr(implode('、', $nicknames), 0, 50),
                'sn'         => ToolService::getInstance()->snowId("QL"),
                'creator_id' => $user->id
            ]);

            $users = [
                [
                    'group_chat_id' => $groupChat->id,
                    'user_id'       => $user->id,
                    'identity'      => GroupChatUserIdentityEnum::Creator->value
                ]
            ];
            foreach ($friends as $friend) {
                $users[] = [
                    'group_chat_id' => $groupChat->id,
                    'user_id'       => $friend->friend_id,
                    'identity'      => GroupChatUserIdentityEnum::Member->value
                ];
            }

            // 创建群聊用户
            $groupChat->users()->createMany($users);
            $groupChat->load(['users.user']);

            // 创建会话
            (new ChatGroup())->payload(
                $groupChat,
                "“{$user->nickname}”邀请你加入群聊",
                MessageTypeEnum::Text->value,
                true
            )
            ->create($user);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }


        return $this->response(['id' => $groupChat->id]);
    }
}