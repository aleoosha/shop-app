<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class RegisterDTO extends Data
{
    public function __construct(
        #[StringType, Min(2)]
        public readonly string $name,

        #[Email, Unique('users', 'email')]
        public readonly string $email,

        #[StringType, Min(8), Confirmed]
        public readonly string $password,
        
        public readonly string $passwordConfirmation,
    ) {}
}
