<?php

use App\Websocket\Controllers\AuthController;
use App\Websocket\Controllers\ChatController;
use App\Websocket\Controllers\ChatSessionController;
use App\Websocket\Controllers\Friend\FriendRequestController;
use App\Websocket\Controllers\NoticeController;
use App\Websocket\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('websocket.')->group(function () {
    /**
     * 授权
     */
    Route::prefix('auth')->group(function () {
        // 登陆
        Route::post('/login', [AuthController::class, 'login']);

        // 刷新 token
        Route::get('/refresh', [AuthController::class, 'refresh']);

        // 注册
        Route::post('/register', [AuthController::class, 'register']);
    });

    /**
     * 授权
     */
    Route::middleware('auth:websocket')->group(function () {
        /**
         * 聊天
         */
        Route::apiResource("chats", ChatController::class);

        /**
         * 会话列表
         */
        Route::apiResource("chat-sessions", ChatSessionController::class);

        /**
         * 用户
         */
        // 当前用户信息
        Route::get('/auth/me', [AuthController::class, 'show']);

        // 用户好友列表
        Route::get('/user/friends', [UserController::class, 'friends']);

        // 搜索
        Route::get('/search', [UserController::class, 'search']);

        /**
         * 好友
         */
        Route::apiResource('friends', FriendRequestController::class);

        /**
         * 好友请求
         */
        Route::apiResource('friend-request', FriendRequestController::class);

        // 好友请求审核
        Route::put('/friend-request/{id}/examine', [FriendRequestController::class, 'examine']);

        /**
         * 通知
         */
        Route::apiResource('notices', NoticeController::class)->only(['index', 'show']);
    });
});


