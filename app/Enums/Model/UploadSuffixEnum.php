<?php

namespace App\Enums\Model;

/**
 * 文件上传后缀类型
 */
enum UploadSuffixEnum: string
{
    case Jpg = 'jpg';
    case Jpeg = 'jpeg';
    case Png = 'png';
    case Gif = 'gif';
    case Mp4 = 'mp4';
    case Mp3 = 'mp3';
    case Excel = 'excel';
    case Word = 'word';
    case Pdf = 'pdf';
    case Txt = 'txt';
    case Markdown = 'markdown';
    case Other = '';
}
