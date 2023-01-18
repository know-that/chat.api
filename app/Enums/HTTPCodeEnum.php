<?php

namespace App\Enums;

/**
 * api 接口 code（非 HTTP status）
 */
enum HTTPCodeEnum: int
{
    /**
     * 成功
     */
    case Success = 0;

    /**
     * 错误
     */
    case Error = 10000; // 默认
    case ErrorArticleViolation = 10100; // 文章违规

    case ErrorAuth = 20000; // 授权类错误
    case ErrorAuthToken = 20001; // token 错误（过期或者错误）
    case ErrorAuthRefreshToken = 20002; // refresh_token 错误（过期或者错误）

    case ErrorAccountAbnormal = 30000; // 账户类错误

    case ErrorPermission = 40000; // 权限类错误

    case ErrorParameter = 50000; // 参数类错误
}
