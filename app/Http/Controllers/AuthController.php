<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\SuccessResponseResource;
use App\Http\Resources\ErrorResponseResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['name', 'email', 'password']);
        $auth_token = Auth::attempt($credentials);

        if ($auth_token) {
            $model = [
                'token' => $auth_token,
                'type' => User::AUTH_TOKEN_TYPE,
                'expires_in' => User::getTokenExpires()
            ];

            return new \App\Http\Resources\LoginResource($model);
        } else {
            return new \App\Http\Resources\ErrorResponseResource(__('auth.failed'), 401);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $model = new User;

            if ($model->saveModel($request->all())) {
                return new SuccessResponseResource(__('auth.register.success'), 201);
            } else {
                return new ErrorResponseResource(__('auth.register.failed'), 500);
            }
        } catch (\Exception $e) {
            return new ErrorResponseResource(__('auth.register.failed'), 500);
        }
    }

    public function currentUser()
    {
        return new \App\Http\Resources\UserResource(Auth::user());
    }
}
