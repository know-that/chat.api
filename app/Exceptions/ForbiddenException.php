<?php

namespace App\Exceptions;

use App\Enums\HTTPCodeEnum;
use App\Enums\HTTPStatusEnum;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ForbiddenException extends BaseException
{
    /**
     * constructor
     *
     * @param string $message
     * @param HTTPCodeEnum $code
     * @param Throwable|null $previous
     * @param HTTPStatusEnum $status
     * @param string $info
     * @param string $link
     */
    #[Pure]
    public function __construct(
        /**
         * 消息
         * @var string
         */
        string $message = "无权访问！",

        /**
         * HTTP 状态码
         * @var int
         */
        HTTPCodeEnum $code = HTTPCodeEnum::ErrorPermission,

        /**
         * 异常
         */
        null|Throwable $previous = null,

        /**
         * 自定义参数 status（HTTP状态码）
         * @var int
         */
        protected HTTPStatusEnum $status = HTTPStatusEnum::Forbidden,

        /**
         * 自定义参数 info （其他信息）
         * @var string
         */
        protected string $info = "",

        /**
         * 自定义参数 link （帮助链接）
         * @var string
         */
        protected string $link = ""
    ) {
        parent::__construct($message, $code, $previous, $status, $info, $link);
    }
}
