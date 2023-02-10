<?php

namespace App\Services\Chat;

use App\Models\User\UserModel;

class ChatService
{
    /**
     * 发送消息
     *
     * @param UserModel $user
     * @param SendSourceFactory $sendSource
     * @return mixed
     */
    public function sendTo(UserModel $user, SendSourceFactory $sendSource): mixed
    {
        return $sendSource->create($user);
    }
}
