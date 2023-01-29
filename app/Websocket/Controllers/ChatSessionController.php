<?php

namespace App\Websocket\Controllers;

use App\Models\Chat\ChatSession;
use App\Models\Notice;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
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
        $sessions = ChatSession::with(['source:id,avatar,nickname', 'lastMessage:id,content,created_at'])
            ->where('user_id', $user->id)
            ->get();
        return $this->response($sessions);
    }
}
