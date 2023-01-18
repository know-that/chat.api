<?php

namespace App\Websocket\Controllers;

use App\Exceptions\ForbiddenException;
use App\Facades\WebsocketFacade;
use App\Models\Chat\Chat;
use App\Models\User\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 聊天
 */
class ChatController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $receiverUserId = $request->input('receiver_user_id');
        $receiverUser = User::findOrFail($receiverUserId);
        $senderUser = $request->user();

        $chats = Chat::where(function ($query) use ($receiverUser, $senderUser) {
                $query->where('receiver_user_id', $receiverUser->id)->where('sender_user_id', $senderUser->id);
            })
            ->orWhere(function ($query) use ($receiverUser, $senderUser) {
                $query->where('receiver_user_id', $senderUser->id)->where('sender_user_id', $receiverUser->id);
            })
            ->orderBy('id', 'desc')
            ->paginate(100);

        // 将所有消息标记已读
        Chat::where('receiver_user_id', $receiverUser->id)->where('sender_user_id', $senderUser->id)->update(['is_read'=>1]);

        return $this->response($chats);
    }

    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ForbiddenException
     */
    public function store(Request $request): JsonResponse
    {
        $params = $request->only(['user_id', 'message']);

        $user = $request->user();

        $receiverUser = User::findOrFail($params['user_id']);
        if (!$receiverUser) {
            throw new ForbiddenException("接收方不存在");
        }
        if ($user->id === $receiverUser->id) {
            throw new ForbiddenException("请勿给自己发送消息");
        }

        // 发送消息给接收方
        $chat = WebsocketFacade::send($user, $receiverUser, $params['message']);

        return $this->response($chat);
    }
}
