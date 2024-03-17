<?php

namespace App\Facades;

use App\Enums\WebsocketMessageTypeEnum;
use App\Models\User\UserModel;
use App\Services\ToolService;
use App\Services\Websocket\WebsocketService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool send(UserModel $user, mixed $data, WebsocketMessageTypeEnum $type = WebsocketMessageTypeEnum::Chat)
 *
 * @see ToolService
 */
class WebsocketFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WebsocketService::class;
    }
}
