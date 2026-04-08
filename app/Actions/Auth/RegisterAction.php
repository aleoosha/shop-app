<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\AuthResponseDTO;
use App\DTOs\Auth\UserDTO;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterAction
{
    public function execute(RegisterDTO $data): AuthResponseDTO
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
            ]);

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
    }
}
