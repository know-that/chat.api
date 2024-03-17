<?php

namespace App\Facades;

use App\Models\User\UserModel;
use App\Services\Chat\ChatService;
use App\Services\Chat\ChatSession;
use App\Services\Chat\SendSourceFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed sendTo(UserModel $user, SendSourceFactory $sendSource)
 *
 * @see ChatSession
 */
class ChatFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ChatService::class;
    }
}
