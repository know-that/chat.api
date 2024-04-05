<?php

namespace App\Websocket\Controllers\Chat;

use App\Exceptions\ForbiddenException;
use App\Facades\ChatFacade;
use App\Models\Chat\ChatSingleModel;
use App\Models\Message\MessageFileModel;
use App\Models\Message\MessageTextModel;
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

        $chats = ChatSingleModel::with([
                'message' => function ($query) {
                    $query->constrain([
                        MessageTextModel::class => function ($query) {
                            $query->selectRaw('id, type, content, is_read, created_at');
                        },
                        MessageFileModel::class => function ($query) {
                            $query->with(['upload:id,suffix,url'])->selectRaw('id, file_id, type, is_read, created_at');
                        },
                    ]);
                }
            ])
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
        $params = $request->only(['user_id', 'message_type', 'message']);
        $user = $request->user();

        $receiverUser = UserModel::findOrFail($params['user_id']);
        if ($user->id === $receiverUser->id) {
            throw new ForbiddenException("请勿给自己发送消息");
        }

        // 创建消息
        $chatSource = (new ChatSingle)->payload($receiverUser, $params['message'], $params['message_type']);
        $chatSingle = ChatFacade::sendTo($user, $chatSource);

        return $this->response($chatSingle);
    }
}
