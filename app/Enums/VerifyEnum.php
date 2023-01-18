<?php

namespace App\Enums;

/**
 * 验证码
 */
enum VerifyEnum: string
{
    use CollectTrait;

    case Register = 'register'; // 注册
    case Login = 'login'; // 登录
    case password = 'password'; // 密码重置

    const REFRESH_SECONDS = 90; // 默认刷新间隔时间/单位秒

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::Register => '注册账户',
            self::Login => '验证码登录',
            self::password => '密码重置'
        };
    }
}
