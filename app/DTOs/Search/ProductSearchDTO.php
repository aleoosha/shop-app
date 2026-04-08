<?php

declare(strict_types=1);

namespace App\DTOs\Search;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapName(SnakeCaseMapper::class)]
class ProductSearchDTO extends Data
{
    public function __construct(
        #[MapInputName('q'), Nullable, StringType]
        public readonly ?string $query,

        #[Numeric, Min(0), Nullable]
        public readonly ?float $minPrice,

        #[Numeric, Min(0), Nullable]
        public readonly ?float $maxPrice,

        #[Numeric, Min(1), Nullable]
        public readonly ?int $categoryId,

        #[Nullable, StringType]
        public readonly ?string $brand,

        #[Nullable, StringType]
        public readonly ?string $color,

        #[Nullable, StringType]
        public readonly ?string $condition,

        #[In(['price', 'title', 'created_at'])]
        public readonly string $sortField = 'price',

        #[In(['asc', 'desc'])]
        public readonly string $sortOrder = 'asc',

        #[Min(1), Max(100)]
        public readonly int $perPage = 15
    ) {}

    public function getElasticSortField(): string
    {
        return match($this->sortField) {
            'title' => 'title.keyword',
            default => $this->sortField,
        };
    }
}
