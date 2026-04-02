<?php

declare(strict_types=1);

namespace App\DTOs\Cart;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class CartItemDTO extends Data
{
    public function __construct(
        public int $productId,

        #[Min(1)]
        public int $quantity = 1,
    ) {}
}
