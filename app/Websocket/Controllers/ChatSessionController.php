<?php

namespace App\Websocket\Controllers;

use App\Models\Chat\ChatSession;
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
        $sessions = ChatSession::with(['source', 'lastMessage:id,content,is_read'])->where('user_id', $user->id)->get();
        return $this->response($sessions);
    }
}
