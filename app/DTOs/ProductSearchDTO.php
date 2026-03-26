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
        public string $sortOrder = 'asc'
    ) {}

    /**
     * Для использования в Контроллерах
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            query: $request->validated('q'),
            minPrice: $request->validated('min_price') ? (float)$request->validated('min_price') : null,
            maxPrice: $request->validated('max_price') ? (float)$request->validated('max_price') : null,
            sortOrder: $request->validated('sort', 'asc')
        );
    }

    /**
     * Для тестов, консоли и очередей
     */
    public static function fromArray(array $data): self
    {
        return new self(
            query: $data['q'] ?? null,
            minPrice: isset($data['min_price']) ? (float)$data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float)$data['max_price'] : null,
            sortOrder: $data['sort'] ?? 'asc'
        );
    }
}
