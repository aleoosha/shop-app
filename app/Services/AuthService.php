<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Login;

class AuthService {
    public function createAuthToken(User $user, string $deviceName): string {
        $token = $user->createToken($deviceName)->plainTextToken;

        event(new Login('sanctum', $user, false));

        return $token;
    }

    public function checkPassword(string $password, string $hashedPassword): bool {
        return Hash::check($password, $hashedPassword);
    }
}