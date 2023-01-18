<?php

namespace App\Enums;

/**
 * HTTP 状态码
 */
enum HTTPStatusEnum: int
{
    /**
     * 请求成功
     */
    case Ok = 200; // 成功
    case Created = 201; // 创建了新的资源
    case Accepted = 202; // 尚未进行处理

    /**
     * 客户端错误
     */
    case Bad = 400;
    case Unauthorized = 401; // 未授权
    case Payment = 402; // 未付款
    case Forbidden = 403; // 拒绝授权访问
    case NotFound = 404; // 资源不存在
    case MethodNotAllowed = 405; // 请求方式不允许
    case ParamBad = 422; // 参数错误

    /**
     * 服务端错误
     */
    case Error = 500; // 服务端错误
    case Unavailable = 503; // 服务不可用

    /**
     * 枚举文本转换
     * @return string
     */
    public function text(): string
    {
        return match ($this) {
            self::Ok => 'OK',
            self::Created => '创建成功',
            self::Accepted => 'Accepted',
            self::Bad => '客户端请求错误',
            self::Unauthorized => '未授权',
            self::Payment => '未付款',
            self::Forbidden => '拒绝授权访问',
            self::ParamBad => '参数错误',
            self::Error => '服务端错误',
            self::NotFound => '资源不存在',
            self::MethodNotAllowed => '请求方式不存在',
            self::Unavailable => '服务不可用',
        };
    }
}
