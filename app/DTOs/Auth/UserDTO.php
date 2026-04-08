<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Models\User;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class UserDTO extends Data
{
    public function __construct(
        public readonly int $id,
        #[Min(3)]
        public readonly string $name,
        #[Email]
        public readonly string $email,
        public readonly ?string $createdAt,
    ) {}
    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            createdAt: $user->created_at?->toDateTimeString(),
        );
    }
}
