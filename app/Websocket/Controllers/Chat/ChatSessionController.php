<?php

namespace App\Websocket\Controllers\Chat;

use App\Facades\ChatSessionFacade;
use App\Models\Chat\ChatSessionModel;
use App\Websocket\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

        $sessions = ChatSessionModel::query()
            ->with(ChatSessionFacade::relations())
            ->where('user_id', $user->id)
            ->orderBy('top_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->response($sessions);
    }
}
