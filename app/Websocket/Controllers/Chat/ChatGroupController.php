<?php

namespace App\Websocket\Controllers\Chat;

use App\Exceptions\ForbiddenException;
use App\Facades\ChatFacade;
use App\Models\Chat\ChatGroupModel;
use App\Models\Chat\ChatSingleModel;
use App\Models\Group\GroupChatModel;
use App\Models\Message\MessageFileModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\UserModel;
use App\Services\Chat\ChatGroup;
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
class ChatGroupController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $groupChatId = $request->input('group_chat_id');
        $groupChat = GroupChatModel::findOrFail($groupChatId);
        $user = $request->user();

        $chats = ChatGroupModel::with([
                'senderUser:id,nickname,account,avatar,gender',
                'senderUser.friend' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->selectRaw('id, user_id, friend_id, alias');
                },
                'message' => function ($query) {
                    $query->constrain([
                        MessageTextModel::class => function ($query) {
                            $query->selectRaw('id, type, content, is_read, created_at');
                        },
                        MessageFileModel::class => function ($query) {
                            $query->with(['upload:id,from,name,suffix,mime,size,url'])->selectRaw('id, file_id, type, is_read, created_at');
                        },
                    ]);
                }
            ])
            ->where('group_chat_id', $groupChat->id)
            ->where('receiver_user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        // 将所有消息标记已读
        ChatGroupModel::query()->where('group_chat_id', $groupChat->id)
            ->where('receiver_user_id', $user->id)
            ->update(['is_read'=>1]);

        return $this->response($chats);
    }

    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $params = $request->only(['group_chat_id', 'message_type', 'message']);
        $user = $request->user();

        $groupChat = GroupChatModel::with('users.user')->findOrFail($params['group_chat_id']);

        // 创建消息
        $chatSource = (new ChatGroup())->payload($groupChat, $params['message'], $params['message_type']);
        $chatSingle = ChatFacade::sendTo($user, $chatSource);

        return $this->response($chatSingle);
    }
}
