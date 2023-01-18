<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // 全局路由参数验证
            $this->routePattern();

            // 用户端 api
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // websocket api
            Route::prefix('websocket')
                ->middleware('api')
                ->group(base_path('routes/websocket.php'));

            // 管理后台 api
            // Route::prefix('admin')
            //     ->middleware('api')
            //     ->group(base_path('routes/admin.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * 路由参数验证
     *
     * @return void
     */
    protected function routePattern(): void
    {
        // id
        Route::pattern('id', "\d+");
    }
}
