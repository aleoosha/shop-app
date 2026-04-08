<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use Spatie\LaravelData\Data;

class AuthResponseDTO extends Data
{
    public function __construct(
        public readonly UserDTO $user,
        public readonly string $accessToken,
        public readonly string $tokenType = 'Bearer',
    ) {}
}
