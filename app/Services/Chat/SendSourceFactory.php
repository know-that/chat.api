<?php

namespace App\Services\Chat;

use App\Models\User\UserModel;

/**
 * 所发送的资源契约
 */
interface SendSourceFactory
{
    /**
     * 创建
     *
     * @param UserModel $user
     * @return mixed
     */
    public function create(UserModel $user): mixed;
}
