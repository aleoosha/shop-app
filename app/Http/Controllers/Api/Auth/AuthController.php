<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterAction;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginDTO $data, LoginAction $action): JsonResponse
    {
        $authResponse = $action->execute($data);

        return $this->success(
            data: $authResponse,
            message: 'Успешный вход'
        );
    }

    public function register(RegisterDTO $data, RegisterAction $action): JsonResponse
    {
        $authResponse = $action->execute($data);

        return $this->success(
            data: $authResponse,
            message: 'Регистрация прошла успешно',
            code: 201
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Выход выполнен');
    }
}
