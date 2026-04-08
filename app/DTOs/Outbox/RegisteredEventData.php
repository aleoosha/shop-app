<?php

declare(strict_types=1);

namespace App\DTOs\Outbox;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\MapName;

#[MapName(SnakeCaseMapper::class)]
class RegisteredEventData extends Data
{
    public function __construct(
        public readonly int $userId,
        
        #[MapInputName('cart_token')]
        public readonly ?string $cartToken,
    ) {}
}
