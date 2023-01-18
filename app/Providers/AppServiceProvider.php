<?php

namespace App\Providers;

use App\Models\Friend\FriendRequest;
use App\Models\Notice;
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
            'friend_request'    => FriendRequest::class,
            'notice'            => Notice::class
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
