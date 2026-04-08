<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Repositories\UserRepositoryContract;
use App\DTOs\Auth\AuthResponseDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\UserDTO;
use App\Jobs\ProcessOutboxEvent;
use App\Services\AuthService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function __construct(
        protected UserRepositoryContract $userRepository,
        protected AuthService $authService
    ) {}

    public function execute(LoginDTO $data): AuthResponseDTO
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (! $user || ! $this->authService->checkPassword($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        $response =  DB::transaction(function () use ($user, $data) {
            $token = $this->authService->createAuthToken($user, $data->deviceName);

            DB::table('outbox_events')->insert([
                'event_type' => Login::class,
                'payload' => json_encode([
                    'user_id' => $user->id,
                    'cart_token' => request()->cookie('cart_token'),
                ]),
                'created_at' => now(),
            ]);

            return new AuthResponseDTO(
                user: UserDTO::from($user),
                accessToken: $token
            );
        });

        ProcessOutboxEvent::dispatch();

        return $response;
    }
}
