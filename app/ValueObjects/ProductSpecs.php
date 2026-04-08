<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

readonly class ProductSpecs implements Arrayable
{
    public function __construct(
        public ?string $brand = null,
        public ?string $color = null,
        public ?string $country = null,
        public ?string $condition = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            brand: $data['brand'] ?? null,
            color: $data['color'] ?? null,
            country: $data['country'] ?? null,
            condition: $data['condition'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'brand' => $this->brand,
            'color' => $this->color,
            'country' => $this->country,
            'condition' => $this->condition,
        ];
    }
}
