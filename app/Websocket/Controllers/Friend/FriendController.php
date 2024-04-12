<?php

namespace App\Websocket\Controllers\Friend;

use App\Exceptions\ResourceException;
use App\Models\Friend\FriendModel;
use App\Models\User\UserModel;
use App\Websocket\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Octane\Exceptions\DdException;
use Throwable;
use function App\Websocket\Controllers\dd;

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
    public function index(Request $request)
    {
        $params = $request->only(['page_size']);
        $user = $request->user();

        $friends = FriendModel::query()
            ->with('user:id,nickname,account,gender,avatar')
            ->where('user_id', $user->id)
            ->normalPaginate($params['page_size'] ?? 15);

        return $this->response($friends);
    }
}
