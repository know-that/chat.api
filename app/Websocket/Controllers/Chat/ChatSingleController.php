<?php

namespace App\Websocket\Controllers\Chat;

use App\Exceptions\ForbiddenException;
use App\Facades\ChatFacade;
use App\Models\Chat\ChatSingleModel;
use App\Models\User\UserModel;
use App\Services\Chat\ChatSingle;
use App\Services\MessageService;
use App\Websocket\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * 聊天
 */
class ChatSingleController extends Controller
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
        $receiverUser = UserModel::findOrFail($receiverUserId);
        $senderUser = $request->user();

        $chats = ChatSingleModel::with('message')
            ->where(function ($query) use ($receiverUser, $senderUser) {
                $query->where('receiver_user_id', $receiverUser->id)->where('sender_user_id', $senderUser->id)->where('is_system', 0);
            })
            ->orWhere(function ($query) use ($receiverUser, $senderUser) {
                $query->where('receiver_user_id', $senderUser->id)->where('sender_user_id', $receiverUser->id);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // 将所有消息标记已读
        (new MessageService)->chatSingleRead($senderUser, $chats->items());

        return $this->response($chats);
    }

    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws GuzzleException
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $params = $request->only(['user_id', 'message']);
        $user = $request->user();

        $receiverUser = UserModel::findOrFail($params['user_id']);
        if (!$receiverUser) {
            throw new ForbiddenException("接收方不存在");
        }
        if ($user->id === $receiverUser->id) {
            throw new ForbiddenException("请勿给自己发送消息");
        }

        // 创建消息
        $chatSource = (new ChatSingle)->payload($receiverUser, $params['message']);
        $chatSingle = ChatFacade::sendTo($user, $chatSource);

        return $this->response($chatSingle);
    }
}
