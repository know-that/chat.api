<?php

namespace App\Services\Websocket;

use App\Enums\WebsocketMessageTypeEnum;
use App\Models\User\UserModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Redis;

class WebsocketService
{
    /**
     * 发送消息
     *
     * @param UserModel $user
     * @param mixed $data
     * @param WebsocketMessageTypeEnum $type
     * @return bool
     * @throws GuzzleException
     */
    public function send(UserModel $user, mixed $data, WebsocketMessageTypeEnum $type = WebsocketMessageTypeEnum::Chat): bool
    {
        // 如果用户不在线则跳过
        $userFd = Redis::get("web-socket:user_id:{$user->id}");
        if (empty($userFd)) {
            return false;
        }

        // 发送消息
        try {
            (new Client())->request('post', 'http://127.0.0.1:9502', [
                'form_params' => [
                    'type'  => $type->value,
                    'fd'    => $userFd,
                    'data'  => $data
                ]
            ]);
        } catch (RequestException $e) {
            return false;
        }

        return true;
    }
}
