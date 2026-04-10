<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Services\LogServiceContract;
use App\DTOs\Auth\AuthResponseDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\UserDTO;
use App\Enums\LogChannel;
use App\Jobs\ProcessOutboxEvent;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterAction
{
    public function __construct(
        private readonly LogServiceContract $logger
    ) {}

    public function execute(RegisterDTO $data): AuthResponseDTO
    {
        $lockKey = 'register_lock_'.md5($data->email);

        $result = Cache::lock($lockKey, 10)->get(function () use ($data) {
            $response = DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                ]);

                $this->logger->info("New user registered: #{$user->id}", [
                    'email' => $user->email,
                ], LogChannel::SYSTEM);

                DB::table('outbox_events')->insert([
                    'event_type' => Registered::class,
                    'payload' => json_encode([
                        'user_id' => $user->id,
                        'cart_token' => request()->cookie('cart_token'),
                    ]),
                    'created_at' => now(),
                ]);

                $token = $user->createToken('auth_token')->plainTextToken;

                return new AuthResponseDTO(
                    user: UserDTO::from($user),
                    accessToken: $token
                );
            });

            $this->logger->action('user_registration', ['email' => $data->email]);
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
