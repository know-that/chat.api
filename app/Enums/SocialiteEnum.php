<?php

namespace App\Enums;

/**
 * 社会化登录
 */
enum SocialiteEnum: string
{
    use CollectTrait;

    case QQ = 'qq'; // 验证码
    case WeiBo = 'weibo'; // 微博
    case Gitee = 'gitee'; // 微博
    case Github = 'github'; // 微博

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::QQ => 'QQ',
            self::WeiBo => '微博',
            self::Gitee => '码云',
            self::Github => 'github'
        };
    }
}
