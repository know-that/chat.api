<?php

namespace App\Facades;

use App\Models\User\User;
use App\Services\WebsocketService;
use Illuminate\Support\Facades\Facade;
use App\Services\ToolService;

/**
 * @method static bool send(User $senderUser, User $receiverUser, string $message)
 *
 * @see ToolService
 * @package App\Facades\AliYun
 */
class WebsocketFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WebsocketService::class;
    }
}
