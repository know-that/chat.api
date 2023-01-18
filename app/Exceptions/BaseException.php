<?php

namespace App\Exceptions;

use App\Enums\HTTPCodeEnum;
use App\Enums\HTTPStatusEnum;
use Illuminate\Support\Facades\Request;
use JetBrains\PhpStorm\Pure;
use Throwable;
use Exception;
use Illuminate\Http\JsonResponse;

class BaseException extends Exception
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
        string $message = "",

        /**
         * HTTP 状态码
         * @var int
         */
        HTTPCodeEnum $code = HTTPCodeEnum::Error,

        /**
         * 异常
         */
        null|Throwable $previous = null,

        /**
         * 自定义参数 status（HTTP状态码）
         * @var int
         */
        protected HTTPStatusEnum $status = HTTPStatusEnum::Error,

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
        parent::__construct(
            !empty($message) ? $message : $this->message,
            $code->value ?? $this->code->value,
            $previous
        );
    }

    /**
     * 将异常渲染至 HTTP 响应值中
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        $data = [
            'code'      => $this->code,
            'message'   => $this->message ?? $this->code->text()
        ];

        if (!empty($this->link)) {
            $data['link'] = $this->link;
        }

        return response()->json($data, $this->status->value);
    }
}
