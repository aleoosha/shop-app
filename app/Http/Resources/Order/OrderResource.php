<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'total_price' => $this->total_price->formatted(),
            'status' => $this->status->value,
            'address' => $this->delivery_address,
            'created_at' => $this->created_at->toDateTimeString(),
            'items_count' => $this->items->count(),
        ];
    }
}
