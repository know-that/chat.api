<?php

namespace App\Http\Middleware;

use App\Enums\HTTPCodeEnum;
use App\Exceptions\AuthException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * 重写未授权方法
     *
     * @param $request
     * @param array $guards
     * @return void
     * @throws AuthException
     */
    protected function unauthenticated($request, array $guards): void
    {
        throw new AuthException(code: HTTPCodeEnum::ErrorAuthToken);
    }
}
