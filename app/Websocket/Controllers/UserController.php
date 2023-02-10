<?php

namespace App\Websocket\Controllers;

use App\Models\User\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 用户
 */
class UserController extends Controller
{
    /**
     * 用户搜索
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $user = UserModel::where('account', $search)->first();

        $list = $user ? [$user] : [];

        return $this->response($list);
    }

    /**
     * 好友列表
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function friends(Request $request): JsonResponse
    {
        $user = $request->user();
        $users = UserModel::whereNotIn('id', [$user->id])->withCount('notReadChats')->get();
        return $this->response($users);
    }
}
