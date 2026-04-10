<?php

declare(strict_types=1);

namespace App\DTOs\Cart;

use Spatie\LaravelData\Data;

class AddToCartResponseDTO extends Data
{
    public function __construct(
        public readonly int $cartCount,
        public readonly int $cartTotal,
        public readonly int $productId,
        public readonly int $quantity,
        public readonly bool $isAvailable,
    ) {}
}
