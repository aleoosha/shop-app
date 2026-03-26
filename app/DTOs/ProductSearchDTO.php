<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ProductSearchDTO
{
    public function __construct(
        public ?string $query,
        public ?float $minPrice,
        public ?float $maxPrice,
        public string $sortField = 'price',
        public string $sortOrder = 'asc',
        public int $perPage = 15
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            query: $request->validated('q'),
            minPrice: $request->validated('min_price') ? (float) $request->validated('min_price') : null,
            maxPrice: $request->validated('max_price') ? (float) $request->validated('max_price') : null,
            sortOrder: $request->validated('sort', 'asc') === 'desc' ? 'desc' : 'asc'
        );
    }
}
