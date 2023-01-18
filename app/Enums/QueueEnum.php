<?php

namespace App\Enums;

/**
 * 队列
 */
enum QueueEnum: string
{
    case VerifyCode = 'verify-code'; // 验证码
    case Message = 'message'; // 消息
    case UserViolationState = 'user:violation:state'; // 用户状态修正
    case AliYunOSSDelete = 'ali-yun:oss:delete'; // 阿里云 OSS 文件删除
}
