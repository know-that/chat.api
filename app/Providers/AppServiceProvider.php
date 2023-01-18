<?php

namespace App\Providers;

use App\Models\Chat\Chat;
use App\Models\Friend\FriendRequest;
use App\Models\Notice;
use App\Models\User\SystemUser;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Relation::morphMap([
            'user'              => User::class,
            'system_user'       => SystemUser::class,
            'friend_request'    => FriendRequest::class,
            'notice'            => Notice::class,
            'chat'              => Chat::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
