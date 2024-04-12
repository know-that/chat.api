<?php

namespace App\Websocket\Controllers\Chat;

use App\Models\Chat\ChatGroupModel;
use App\Models\Chat\ChatNoticeModel;
use App\Models\Friend\FriendModel;
use App\Models\Friend\FriendRequestModel;
use App\Models\User\SystemUserModel;
use App\Models\User\UserModel;
use App\Services\MessageService;
use App\Websocket\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * 通知
 */
class ChatNoticeController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $params = $request->only(['source_type', 'limit']);

        $notices = ChatNoticeModel::query()
            ->with([
                'source' => function (MorphTo $query) {
                    $query->constrain([
                        FriendRequestModel::class => function ($query) {
                            $query->with('user')->selectRaw('id, user_id, remark, state, reason, created_at');
                        },
                        SystemUserModel::class => function ($query) {
                            $query->selectRaw('id, type, nickname, avatar');
                        }
                    ]);
                },
                'message'
            ])
            ->where('user_id', $user->id);

        if (!empty($params['source_type'])) {
            $notices->where('source_type', $params['source_type']);
        }

        $notices = $notices->orderBy('id', 'desc')->paginate($params['limit'] ?? 10);

        // 将所有消息标记已读
        ChatNoticeModel::query()->where('user_id', $user->id)->update(['is_read'=>1]);

        Cache::delete("chat-sessions:{$user->id}");

        return $this->response($notices);
    }

    /**
     * 详情
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $notice = ChatNoticeModel::with([
                'source.user',
                'source.friend'
            ])
            ->where('user_id', $user->id)
            ->find($id);

        return $this->response($notice);
    }
}
