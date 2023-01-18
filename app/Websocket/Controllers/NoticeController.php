<?php

namespace App\Websocket\Controllers;

use App\Models\Notice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 通知
 */
class NoticeController extends Controller
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
        $params = $request->only(['limit']);

        $notices = Notice::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($params['limit'] ?? 15);

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
        $notice = Notice::with([
                'source.user',
                'source.friend'
            ])
            ->where('user_id', $user->id)
            ->find($id);

        return $this->response($notice);
    }
}
