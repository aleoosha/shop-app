<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class LoginDTO extends Data
{
    public function __construct(
        #[Required, StringType, Email]
        public readonly string $email,

        #[Required, StringType]
        public readonly string $password,

        #[Required, StringType, Max(255)]
        public readonly string $deviceName,
    ) {}
}