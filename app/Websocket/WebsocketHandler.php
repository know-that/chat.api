<?php

namespace App\Websocket;

use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class WebsocketHandler
{
    /**
     * websocket 服务
     * @var Server
     */
    protected Server $server;

    /**
     * 客户端识别号
     * @var int
     */
    protected int $fd;

    /**
     * constructor
     *
     * @param Server $server
     * @param int $fd
     */
    public function __construct(Server $server, int $fd)
    {
        $this->server = $server;
        $this->fd = $fd;
    }

    public function start(): void
    {
        echo 'start';
    }

    /**
     * @param Request $req
     * @return void
     */
    public function open(Request $req): void
    {
        try {
            $token = str_replace("Bearer ", "", $req->header['authorization'] ?? $req->get['token']);
            $userId = Auth::guard('api')->setToken($token)->payload()->get('sub');
            $user = User::findOrFail($userId);
        } catch (\Throwable $e) {
            $this->disconnect("您尚未登录或者您的登录信息已失效");
            return;
        }

        // 将 fd 与 user_id 绑定
        Redis::set("web-socket:user_id:{$user->id}", $req->fd);
        Redis::set("web-socket:fd:{$req->fd}", $user->id);
    }

    public function message($frame): void
    {
        echo 'message';
    }

    public function close($fd): void
    {
        try {
            // 将 fd 与 user_id 解绑
            $userId = Redis::get("web-socket:fd:{$fd}");
            Redis::del("web-socket:user_id:{$userId}");
            Redis::del("web-socket:fd:{$fd}");
        } catch (\Throwable $e) {
        }
    }

	/**
	 * 接收请求
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @return mixed
	 */
    public function request(Request $request, Response $response): mixed
	{
        $fd = $request->post['receiver_fd'] ?? null;
        $message = $request->post['message'] ?? null;

        if (empty($request->post) || empty($fd) && empty($message)) {
            return 111;
        }

        if (!$this->server->isEstablished($fd)) {
            return 222;
        }

        $this->server->push(
            $fd,
            json_encode(
                ['message'=>$message]
            )
        );
    }

    /**
     * 断开连接
     *
     * @param string $message
     * @return void
     */
    protected function disconnect(string $message): void
    {
        $this->server->push($this->fd, json_encode(['message'=>$message], JSON_UNESCAPED_UNICODE));
        $this->server->disconnect($this->fd);
    }
}
