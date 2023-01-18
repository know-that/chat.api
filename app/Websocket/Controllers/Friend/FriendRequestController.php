<?php

namespace App\Websocket\Controllers\Friend;

use App\Exceptions\ForbiddenException;
use App\Models\Chat\ChatSession;
use App\Models\Friend\Friend;
use App\Models\Friend\FriendRequest;
use App\Models\Notice;
use App\Models\User\User;
use App\Websocket\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * 好友请求
 */
class FriendRequestController extends Controller
{
    /**
     * 创建
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $params = $request->only(['friend_id', 'remark']);
        $friend = User::findOrFail($params['friend_id']);

        // 消息数据
        $noticeData = [
            [
                'user_id'     => $friend->id,
                'content'     => "{$user->nickname} 请求添加你为好友"
            ],
            [
                'user_id'     => $user->id,
                'content'     => "你向 {$user->nickname} 发起了好友请求"
            ]
        ];

        DB::beginTransaction();
        try {
            // 创建请求
            $friendRequest = FriendRequest::create([
                'user_id'   => $user->id,
                'friend_id' => $friend->id,
                'remark'    => $params['remark'] ?? ''
            ]);

            // 发送通知
            $notices = $friendRequest->notices()->createMany($noticeData);

            // 创建会话
            foreach ($notices as $notice) {
                ChatSession::updateOrCreate([
                    'user_id'           => $notice->user_id,
                    'source_type'       => 'system_user',
                    'source_id'         => 1
                ], [
                    'last_message_type' => 'notice',
                    'last_message_id'   => $notice->id
                ]);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->response();
    }

    /**
     * 审核
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function examine(Request $request, int $id): JsonResponse
    {
        $params = $request->only(['state', 'reason']);
        $user = $request->user();
        $friendRequest = FriendRequest::with(['user', 'friend'])->where('friend_id', $user->id)->findOrFail($id);

        if ($friendRequest->state !== 0) {
            throw new ForbiddenException("会话已过期");
        }

        // 审核数据
        $friendRequest->state = $params['state'];
        $friendRequest->reason = $params['reason'] ?? '';

        $word = $params['state'] === 10 ? "同意" : "拒绝";

        // 消息数据
        $noticeData = [
            [
                'source_type'   => 'friend_request',
                'source_id'     => $friendRequest->id,
                'user_id'       => $friendRequest->user->id,
                'content'       => "{$friendRequest->user->nickname} {$word}了你的请求"
            ],
            [
                'source_type'   => 'friend_request',
                'source_id'     => $friendRequest->id,
                'user_id'       => $friendRequest->friend->id,
                'content'       => "你{$word}了 {$friendRequest->friend->nickname} 的请求"
            ]
        ];

        // 用户会话
        $sessions = ChatSession::whereIn('user_id', [$friendRequest->user->id, $friendRequest->friend->id])
            ->where('source_type', 'system_user')
            ->where('source_id', '1')
            ->get();

        DB::beginTransaction();
        try {
            $friendRequest->save();

            // 发送通知
            $notices = $friendRequest->notices()->createMany($noticeData);

            // 更新会话最新记录
            foreach ($sessions as $session) {
                foreach ($notices as $notice) {
                    if ($session->user_id === $notice->user_id) {
                        $session->last_message_id = $notice->id;
                        $session->save();
                        break;
                    }
                }
            }

            // 如果是同意则添加好友
            if ((int) $params['state'] === 10) {
                // 添加好友
                Friend::create([
                    'user_id'       => $friendRequest->user->id,
                    'friend_type'   => 'user',
                    'friend_id'     => $friendRequest->friend->id
                ]);
                Friend::create([
                    'user_id'       => $friendRequest->friend->id,
                    'friend_type'   => 'user',
                    'friend_id'     => $friendRequest->user->id
                ]);

                // 创建会话
                ChatSession::create([
                    'user_id'       => $friendRequest->friend->id,
                    'source_type'   => 'user',
                    'source_id'     => $friendRequest->user->id
                ]);
                ChatSession::create([
                    'user_id'       => $friendRequest->user->id,
                    'source_type'   => 'user',
                    'source_id'     => $friendRequest->friend->id
                ]);

                // 发送实时消息
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->response();
    }
}
