<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;

class CheckoutDTO extends Data
{
    public function __construct(
        #[Required, Min(10)]
        public readonly string $address,

        #[Required, Min(10)]
        public readonly string $phone,

        public readonly ?string $note = null,
    ) {}
}
