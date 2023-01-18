<?php

namespace App\Services;

use App\Models\Chat\Chat;
use App\Models\Chat\ChatSession;
use App\Models\User\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class WebsocketService
{
    /**
     * 发送消息
     *
     * @param User $senderUser
     * @param User $receiverUser
     * @param string $message
     * @return mixed
     * @throws GuzzleException
     * @throws Throwable
     */
    public function send(User $senderUser, User $receiverUser, string $message): mixed
    {
        $receiverFd = Redis::get("web-socket:user_id:{$receiverUser->id}");

        DB::beginTransaction();
        try {
            // 创建消息
            $chat = Chat::create([
                'receiver_user_id'  => $senderUser->id,
                'sender_user_id'    => $receiverUser->id,
                'content'           => $message
            ]);

            // 创建发送方会话
            ChatSession::updateOrcreate([
                'user_id'       => $senderUser->id,
                'source_type'   => 'user',
                'source_id'     => $receiverUser->id
            ],[
                'last_message_type' => 'chat',
                'last_message_id'   => $chat->id
            ]);

            // 创建接收方会话
            ChatSession::updateOrcreate([
                'user_id'       => $receiverUser->id,
                'source_type'   => 'user',
                'source_id'     => $senderUser->id
            ], [
                'last_message_type' => 'chat',
                'last_message_id'   => $chat->id
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        try {
            // 如果用户在线则发送消息
            if ($receiverFd) {
                (new Client())->request('post', 'http://127.0.0.1:9502', [
                    'form_params' => [
                        'receiver_fd'   => $receiverFd,
                        'message'       => $chat->toJson()
                    ]
                ]);
            }
        } catch (RequestException $e) {
        }
        return $chat;
    }
}
