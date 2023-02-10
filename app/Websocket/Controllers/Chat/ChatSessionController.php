<?php

namespace App\Websocket\Controllers\Chat;

use App\Models\Chat\ChatNoticeModel;
use App\Models\Chat\ChatSessionModel;
use App\Models\Chat\ChatSingleModel;
use App\Models\Message\MessageTextModel;
use App\Models\User\SystemUserModel;
use App\Models\User\UserModel;
use App\Websocket\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 会话列表
 */
class ChatSessionController extends Controller
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
        $sessions = ChatSessionModel::with([
                'source' => function ($query) {
                    $query->constrain([
                        UserModel::class => function ($query) {
                            $query->selectRaw('id, nickname, account, avatar, gender');
                        },
                        SystemUserModel::class => function ($query) {
                            $query->selectRaw('id, type, nickname, avatar');
                        },
                    ]);
                },
                'lastChat' => function ($query) {
                    $query->with('message', function ($query) {
                            $query->constrain([
                                MessageTextModel::class => function ($query) {
                                    $query->selectRaw('id, type, content, is_read, created_at');
                                },
                            ]);
                        })
                        ->constrain([
                            ChatNoticeModel::class => function ($query) {
                                $query->selectRaw('id, user_id, source_type, source_id, message_type, message_id, created_at');
                            },
                            ChatSingleModel::class => function ($query) {
                                $query->selectRaw('id, receiver_user_id, sender_user_id, message_type, message_id, created_at');
                            },
                        ]);
                }
            ])
            ->where('user_id', $user->id)
            ->orderBy('top_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return $this->response($sessions);
    }
}
