<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use App\Models\Order;
use Spatie\LaravelData\Data;

class OrderResponseDTO extends Data
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $totalPrice,
        public readonly string $status,
        public readonly string $createdAt,
    ) {}

    public static function fromModel(Order $order): self
    {
        return new self(
            uuid: $order->uuid,
            totalPrice: $order->total_price->formatted(),
            status: $order->status->value,
            createdAt: $order->created_at->toDateTimeString(),
        );
    }
}
