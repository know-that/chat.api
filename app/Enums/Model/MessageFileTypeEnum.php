<?php

namespace App\Enums\Model;

/**
 * 消息资源类型
 */
enum MessageFileTypeEnum: string
{
    case Jpg = 'jpg';
    case Jpeg = 'jpeg';
    case Png = 'png';
    case Gif = 'gif';
    case Video = 'video';
    case Audio = 'audio';
    case Excel = 'excel';
    case Word = 'word';
    case Pdf = 'pdf';
    case Txt = 'txt';
    case Markdown = 'markdown';
    case Other = '';

    /**
     * 枚举文本转换
     * @param string $value
     * @return string
     */
    public function text(string $value = ''): string
    {
        return match ($this) {
            self::Jpg,
            self::Jpeg,
            self::Png => '图片',
            self::Gif => '动态表情',
            self::Video => '视频',
            self::Audio => '音频',
            self::Excel => '表格',
            self::Word => 'word文档',
            self::Pdf => 'pdf文档',
            self::Txt => 'txt文件',
            self::Markdown => 'markdown文档',
            self::Other => "{$value}文件"
        };
    }
}
