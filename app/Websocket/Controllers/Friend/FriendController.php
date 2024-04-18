<?php

namespace App\Websocket\Controllers\Friend;

use App\Models\Friend\FriendModel;
use App\Websocket\Controllers\Controller;
use App\Websocket\Requests\Friend\FriendAliasRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 好友
 */
class FriendController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->only(['page_size']);
        $user = $request->user();

        $friends = FriendModel::query()
            ->with('user:id,nickname,account,gender,avatar')
            ->where('user_id', $user->id)
            ->normalPaginate($params['page_size'] ?? 15);

        return $this->response($friends);
    }

    /**
     * 设置好友别名
     *
     * @param FriendAliasRequest $request
     * @param int                $id
     * @return JsonResponse
     */
    public function updateAlias(FriendAliasRequest $request, int $id): JsonResponse
    {
        $params = $request->only(['alias']);
        $user = $request->user();
        $friend = FriendModel::query()->where('user_id', $user->id)->findOrFail($id);
        $friend->alias = $params['alias'];
        $friend->save();

        return $this->response();
    }
}
