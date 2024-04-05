<?php

namespace App\Enums\Model;

/**
 * 消息资源类型
 */
enum MessageFileTypeEnum: string
{
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case File = 'file';

    /**
     * 枚举文本转换
     * @param string $value
     * @return string
     */
    public function text(string $value = ''): string
    {
        return match ($this) {
            self::Image => '图片',
            self::Video => '视频',
            self::Audio => '音频',
            self::File => "{$value}文件"
        };
    }

    /**
     * 根据后缀获取类型
     *
     * @param string $value
     * @return string
     */
    public static function getTypeBySuffix(string $value = ''): string
    {
        return match ($value) {
            UploadSuffixEnum::Jpeg->value,
            UploadSuffixEnum::Jpg->value,
            UploadSuffixEnum::Gif->value,
            UploadSuffixEnum::Png->value => '图片',
            UploadSuffixEnum::Mp4->value => '视频',
            UploadSuffixEnum::Mp3->value => '音频',
            default => "{$value}文件"
        };
    }
}
