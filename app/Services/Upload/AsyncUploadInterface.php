<?php

namespace App\Services\Upload;

use App\Models\Upload;
use App\Models\User\UserModel;

/**
 * 所发送的资源契约
 */
interface AsyncUploadInterface
{
    /**
     * 授权信息
     * @return mixed
     */
    public function credentials(): array;

    /**
     * 回调
     * @param array $params
     * @return Upload
     */
    public function callback(array $params): Upload;
}
