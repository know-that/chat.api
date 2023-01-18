<?php

namespace App\Websocket\Controllers;

use App\Http\Controllers\Controller as HttpController;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Swoole\WebSocket\Server as WSServer;
use App\Traits\ResponseTrait;

class Controller extends HttpController
{
    use ResponseTrait;
}
