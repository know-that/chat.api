<?php

namespace App\Enums\Model;

use App\Enums\CollectTrait;

/**
 * 文件上传来源
 */
enum FileUploadFromEnum: int
{
    use CollectTrait;

    case Local = 0; // 本地
    case Other = 1; // 其他
    case AliYun = 2; // 阿里云 oss
    case QiNiu = 3; // 七牛云 kodo

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::Local => '本地',
            self::Other => '其他',
            self::AliYun => '阿里云',
            self::QiNiu => '七牛云',
        };
    }
}
