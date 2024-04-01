<?php

namespace App\Facades;

use App\Models\User\UserModel;
use App\Services\ChatSessionService;
use Illuminate\Support\Facades\Facade;
use App\Services\ToolService;

/**
 * @method static array relations(UserModel $user)
 *
 * @see ToolService
 */
class ChatSessionFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ChatSessionService::class;
    }
}
