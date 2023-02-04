<?php

namespace App\Enums\Model;

use App\Enums\CollectTrait;

/**
 * 好友请求审核状态
 */
enum FriendRequestStateEnum: int
{
    use CollectTrait;

    case Waiting = 0; // 待审核
    case Agreed = 10; // 已同意
    case Rejected = 20; // 已拒绝
}
