<?php

namespace App\Websocket\Controllers;

use App\Facades\ToolFacade;
use App\Websocket\Requests\Auth\LoginRequest;
use App\Websocket\Requests\Auth\RegisterRequest;
use App\Enums\HTTPCodeEnum;
use App\Models\User\User;
use App\Exceptions\ErrorException;
use App\Exceptions\ParameterException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\AuthException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use \Exception;
use Throwable;

/**
 * 授权
 *
 * Class AuthController
 * @package App\Api\Controllers\
 */
class AuthController extends Controller
{
    /**
     * 注册
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws ErrorException
     * @throws ForbiddenException
     * @throws Exception
     * @see \App\Providers\RouteServiceProvider
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $params = $request->only(['account', 'password']);
        $nickname = ToolFacade::strHidden($params['account']);

        // 检测账号是否被使用
        $exists = User::where('account', $params['account'])->exists();
        if ($exists) {
            throw new ForbiddenException("账号 {$nickname} 已被使用！");
        }

        // 创建用户
        DB::beginTransaction();
        try {
            $user = User::create([
                'account'   => $params['account'],
                'nickname'  => $nickname,
                'password'  => Hash::make($params['password']),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        // 生成 token
        $token = Auth::login($user);
        if (!$token) {
            throw new ErrorException();
        }

        return $this->response([
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60
        ], "注册成功，已自动登录");
    }

    /**
     * 账号密码登陆
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     * @throws ErrorException
     * @throws ParameterException
     * @throws AuthException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $params = $request->only(['account', 'password']);
        $user = User::where('account', $params['account'])->first();
        if (!$user) {
            throw new ParameterException("账号或密码错误");
        }

        if (!Hash::check($params['password'], $user->password)) {
            throw new ParameterException("账号或密码错误");
        }

        if ($user->is_banned) {
            throw new AuthException("您已被封号，请联系管理员", code: HTTPCodeEnum::ErrorAccountAbnormal);
        }

        $token = Auth::login($user);
        if (!$token) {
            throw new ErrorException();
        }

        return $this->response([
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60
        ], "欢迎回家，亲爱的 {$user->nickname}");
    }

    /**
     * 修改密码
     *
     * @param RegisterRequest $request
     * @param string $registerType
     * @return JsonResponse
     * @throws ForbiddenException
     */
    public function password(RegisterRequest $request, string $registerType): JsonResponse
    {
        $params = $request->only(['account', 'password', 'verify_code']);
        $nickname = ToolFacade::strHidden($params['account']);

        // 检测账号是否被使用
        $user = User::where($registerType, $params['account'])->first();
        if (!$user) {
            throw new ForbiddenException("账号 {$nickname} 不存在！");
        }

        $user->password = Hash::make($params['password']);
        $user->save();

        return $this->response();
    }

    /**
     * 当前用户基本信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->response([
            'id'        => $user->id,
            'account'   => $user->account,
            'nickname'  => $user->nickname,
            'gender'    => $user->gender,
            'avatar'    => $user->avatar
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     * @throws AuthException
     */
    public function refresh(): JsonResponse
    {
        try {
            $data = Auth::refresh();
        } catch (Throwable) {
            throw new AuthException("登录信息已过期，请重新登录", code: HTTPCodeEnum::ErrorAuthRefreshToken);
        }

        return $this->response([
            'access_token'  => $data,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->response();
    }
}
