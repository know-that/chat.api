<?php

namespace App\Enums\Model;

use App\Enums\CollectTrait;

/**
 * 系统用户类型
 */
enum SystemUserTypeEnum: int
{
    use CollectTrait;

    case Announcement = 1; // 系统消息
    case FriendRequest = 2; // 好友请求

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::Announcement => '系统消息',
            self::FriendRequest => '好友请求'
        };
    }
}
