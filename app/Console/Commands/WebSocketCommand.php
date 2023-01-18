<?php

namespace App\Console\Commands;

use App\Models\User\User;
use App\Websocket\Controllers\WebSocketController;
use App\Websocket\WebsocketHandler;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Swoole\Websocket\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Support\Facades\App;

class WebSocketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected Server $server;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->server = new Server('0.0.0.0', 9502);

        $this->server->on('start', $this->callTo('start'));
        $this->server->on('open', $this->callTo('open'));
        $this->server->on('message', $this->callTo('message'));
        $this->server->on('close', $this->callTo('close'));
        $this->server->on('request', $this->callTo('request'));

        $this->server->start();

    }

    /**
     * @param string $command
     * @return Closure
     */
    public function callTo(string $command): Closure
    {
        return function (...$params) use ($command) {
            // 获取 server
            $server = $command === 'request' ? $this->server : array_shift($params);

            // 获取 fd
            $fd = $params[0]->fd ?? $params[0] ?? 0;

            // 请求类方法
            $handler = app()->make(WebsocketHandler::class, [
                'server' => $server,
                'fd'     => $fd
            ]);
            call_user_func_array([$handler, $command], $params);
        };
    }
}
