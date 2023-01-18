<?php

namespace App\Exceptions;

use App\Enums\HTTPCodeEnum;
use App\Enums\HTTPStatusEnum;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Swoole\Coroutine\Socket\Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthException::class,
        ForbiddenException::class,
        ParameterException::class,
        ResourceException::class
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        // 报告异常
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e)) {
                // 如果是 HTTP 异常则记录参数
                $paramText = $e instanceof HttpExceptionInterface ? "【请求参数】\n" . json_encode(Request::all()) . "\n" : '';

                // 自定义日志内容
                Log::error(
                    $e->getMessage(). "\n" .
                    "【异常类】" . $e::class . "\n" .
                    "【错误文件】{$e->getFile()}:{$e->getLine()}\n" .
                    $paramText .
                    "【错误堆栈】\n" . $e->getTraceAsString() . "\n"
                );
                return false; // 阻止默认报错日志记录
            }

            return true;
        });

        // 自定义 http 异常渲染
        $this->renderable(function (HttpExceptionInterface $e) {
            $message = HTTPStatusEnum::tryFrom($e->getStatusCode())->text() ?? HTTPStatusEnum::Unavailable->text();
            return $this->response(
                message: $message,
                status: HTTPStatusEnum::tryFrom($e->getStatusCode()),
                code: HTTPCodeEnum::tryFrom($e->getCode())
            );
        });

        // 自定义 websocket 异常渲染
        $this->renderable(function (Exception $e) {
            dump($e->getMessage());
        });
    }
}
