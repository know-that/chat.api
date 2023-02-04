<?php

namespace App\Websocket\Controllers\Chat;

use App\Models\Chat\ChatNotice;
use App\Models\Friend\Friend;
use App\Models\Friend\FriendRequest;
use App\Models\User\SystemUser;
use App\Models\User\User;
use App\Websocket\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $notices = ChatNotice::query()
            ->with([
                'source' => function (MorphTo $query) {
                    $query->constrain([
                        FriendRequest::class => function ($query) {
                            $query->with('user')->selectRaw('id, user_id, remark, state, reason, created_at');
                        },
                        SystemUser::class => function ($query) {
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
        $notice = ChatNotice::with([
                'source.user',
                'source.friend'
            ])
            ->where('user_id', $user->id)
            ->find($id);

        return $this->response($notice);
    }
}
