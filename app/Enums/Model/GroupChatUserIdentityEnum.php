<?php

namespace App\Enums\Model;

/**
 * 消息资源类型
 */
enum GroupChatUserIdentityEnum: int
{
    case Member = 0;
    case Creator = 1;
    case Manage = 2;

    /**
     * 枚举文本转换
     * @param string $value
     * @return string
     */
    public function text(string $value = ''): string
    {
        return match ($this) {
            self::Member => '成员',
            self::Creator => '群主',
            self::Manage => '管理员'
        };
    }
}
