<?php

namespace App\Websocket\Controllers\Friend;

use App\Enums\Model\FriendRequestStateEnum;
use App\Enums\RelationEnum;
use App\Exceptions\ForbiddenException;
use App\Models\Chat\ChatSession;
use App\Models\Friend\FriendRequest;
use App\Models\Message\MessageText;
use App\Models\User\User;
use App\Services\FriendService;
use App\Websocket\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * 好友请求
 */
class FriendRequestController extends Controller
{
    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $params = $request->only(['friend_id', 'remark']);
        $friend = User::findOrFail($params['friend_id']);

        if ($friend->id === $user->id) {
            throw new ForbiddenException("无需添加自己为好友");
        }

        // 判断此用户是否已发送请求（非原子性）
        $friendRequest = FriendRequest::where('user_id', $user->id)->where('friend_id', $friend->id)->orderBy('id', 'desc')->first();
        if ($friendRequest) {
            if ($friendRequest->state === FriendRequestStateEnum::Waiting->value) {
                throw new ForbiddenException("您已发起过好友请求");
            }
            if ($friendRequest->state === FriendRequestStateEnum::Agreed->value) {
                throw new ForbiddenException("该用户已经是你的好友或已同意你的好友请求");
            }
        }

        DB::beginTransaction();
        try {
            // 创建请求
            $friendRequest = FriendRequest::create([
                'user_id'   => $user->id,
                'friend_id' => $friend->id,
                'remark'    => $params['remark'] ?? ''
            ]);

            // 创建消息
            $messageText = MessageText::create([
                'content'     => "{$user->nickname} 请求添加你为好友"
            ]);

            // 发送通知
            $chatNotice = $messageText->notice()->create([
                'user_id'       => $friend->id,
                'source_type'   => RelationEnum::FriendRequest->getName(),
                'source_id'     => $friendRequest->id
            ]);

            // 创建会话
            ChatSession::updateOrCreate([
                'user_id'           => $friend->id,
                'source_type'       => RelationEnum::SystemUser->getName(),
                'source_id'         => 2
            ], [
                'last_chat_type'    => RelationEnum::ChatNotice->getName(),
                'last_chat_id'      => $chatNotice->id
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->response();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function examine(Request $request, int $id): JsonResponse
    {
        $params = $request->only(['state', 'reason']);
        $user = $request->user();
        $friendRequest = FriendRequest::with(['user', 'friend'])->where('friend_id', $user->id)->findOrFail($id);

        if ($friendRequest->state !== 0) {
            throw new ForbiddenException("会话已过期");
        }

        // 审核数据
        $friendRequest->state = $params['state'];
        $friendRequest->reason = $params['reason'] ?? '';

        DB::beginTransaction();
        try {
            $friendRequest->save();

            // 如果是同意则添加好友
            if ((int) $params['state'] === FriendRequestStateEnum::Agreed->value) {
                // 绑定好友关系
                (new FriendService)->bind($friendRequest, $friendRequest->user, $friendRequest->friend);

                // 发送实时消息
            } else {
                // 发送拒绝消息
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->response();
    }
}
