<?php

namespace App\Websocket\Controllers\Friend;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ResourceException;
use App\Models\Chat\ChatSession;
use App\Models\Friend\Friend;
use App\Models\Friend\FriendRequest;
use App\Models\Notice;
use App\Models\User\User;
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
     * 发送请求
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws ResourceException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $params = $request->only(['friend_id']);
        $user = $request->user();

        $friendUser = User::where('id', $params['friend_id'])->first();
        if (!$friendUser) {
            throw new ResourceException("用户不存在");
        }
        if ($user->id === $friendUser->id) {
            throw new ForbiddenException("无需添加自己");
        }

        // 判断是否有该好友
        $friend = Friend::where('friend_type', 'user')->where('friend_id', $friendUser->id)->first();
        if ($friend) {
            throw new ForbiddenException("已添加");
        }

        // 判断是否已发送请求
        $friendRequest = FriendRequest::where('friend_type', 'user')
            ->where('friend_id', $friendUser->id)
            ->where('state', 10)
            ->first();
        if ($friendRequest) {
            throw new ForbiddenException("请勿频繁发送请求");
        }

        DB::beginTransaction();
        try {
            // 发送请求
            $friendRequest = FriendRequest::create([
                'user_id'       => $user->id,
                'friend_type'   => 'user',
                'friend_id'     => $friendUser->id
            ]);

            // 发送好友请求通知
            $notice = Notice::create([
                'user_id'       => $friendUser->id,
                'source_type'   => 'friend_request',
                'source_id'     => $friendRequest->id,
                'content'       => "{$user->nickname} 请求添加你为好友"
            ]);

            // 创建接收方会话
            ChatSession::create([
                'user_id'           => $friendUser->id,
                'source_type'       => 'friend_request',
                'source_id'         => $friendRequest->id,
                'last_message_type' => 'notice',
                'last_message_id'   => $notice->id
            ]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            if ((int) $e->getCode() !== 23000) {
                throw $e;
            }
        }

        return $this->response(message: "已发送好友请求");
    }
}
