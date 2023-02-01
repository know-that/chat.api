<?php

namespace App\Websocket\Controllers\Chat;

use App\Models\Chat\ChatNotice;
use App\Models\Chat\ChatSession;
use App\Models\Chat\ChatSingle;
use App\Models\Message\MessageText;
use App\Models\User\SystemUser;
use App\Models\User\User;
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
        $sessions = ChatSession::with([
                'source' => function ($query) {
                    $query->constrain([
                        User::class => function ($query) {
                            $query->selectRaw('id, nickname, account, avatar, gender');
                        },
                        SystemUser::class => function ($query) {
                            $query->selectRaw('id, type, nickname, avatar');
                        },
                    ]);
                },
                'lastChat' => function ($query) {
                    $query->with('message', function ($query) {
                            $query->constrain([
                                MessageText::class => function ($query) {
                                    $query->selectRaw('id, type, content, is_read, created_at');
                                },
                            ]);
                        })
                        ->constrain([
                            ChatNotice::class => function ($query) {
                                $query->selectRaw('id, user_id, source_type, source_id, message_type, message_id, created_at');
                            },
                            ChatSingle::class => function ($query) {
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
