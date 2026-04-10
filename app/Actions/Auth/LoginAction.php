<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Contracts\Services\LogServiceContract;
use App\DTOs\Auth\AuthResponseDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\UserDTO;
use App\Enums\LogChannel;
use App\Jobs\ProcessOutboxEvent;
use App\Services\AuthService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    public function __construct(
        protected UserRepositoryContract $userRepository,
        protected AuthService $authService,
        protected LogServiceContract $logger
    ) {}

    public function execute(LoginDTO $data): AuthResponseDTO
    {
        $lockKey = "login_lock_" . md5($data->email);

        $result = Cache::lock($lockKey, 5)->get(function () use ($data) {
            $user = $this->userRepository->findByEmail($data->email);

            if (! $user || ! $this->authService->checkPassword($data->password, $user->password)) {
                $this->logger->warning("Failed login attempt", [
                    'email' => $data->email,
                ], LogChannel::SECURITY);

                throw ValidationException::withMessages([
                    'email' => ['Неверные учетные данные.'],
                ]);
            }

            $response = DB::transaction(function () use ($user, $data) {
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

            $this->logger->action('user_login', ['user_id' => $user->id]);

            ProcessOutboxEvent::dispatch();

            return $response;

        });

        if ($result === false) {
             throw ValidationException::withMessages([
                'email' => ['Слишком много попыток входа.'],
            ]);
        }

        return $result;
    }
}
