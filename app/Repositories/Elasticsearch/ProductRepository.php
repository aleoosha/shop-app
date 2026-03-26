<?php

declare(strict_types=1);

namespace App\Repositories\Elasticsearch;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\ProductSearchDTO;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use stdClass;

class ProductRepository implements ProductRepositoryContract
{
    public function autocomplete(string $query): \Illuminate\Support\Collection
    {
        return Product::searchQuery()
            ->query([
                'match_phrase_prefix' => [
                    'title' => [
                        'query' => $query
                    ]
                ]
            ])
            ->size(10)
            ->execute()
            ->models();
    }

    public function search(ProductSearchDTO $data): LengthAwarePaginator
    {
        $builder = Product::searchQuery();

        $must = $data->query 
            ? ['multi_match' => ['query' => $data->query, 'fields' => ['title', 'description'], 'fuzziness' => 'AUTO']]
            : ['match_all' => new stdClass()];

        $filters = $this->buildFilters($data);

        return $builder->query([
            'bool' => [
                'must' => [$must],
                'filter' => $filters,
            ],
        ])
        ->highlight('description', [
            'require_field_match' => false,
            'pre_tags' => ['<em class="highlight">'],
            'post_tags' => ['</em>'],
        ])
        ->sort($data->sortField, $data->sortOrder)
        ->paginate($data->perPage);
    }

    private function buildFilters(ProductSearchDTO $data): array
    {
        $filters = [];

        if ($data->minPrice !== null || $data->maxPrice !== null) {
            $range = [];
            if ($data->minPrice !== null) $range['gte'] = $data->minPrice;
            if ($data->maxPrice !== null) $range['lte'] = $data->maxPrice;

            $filters[] = ['range' => ['price' => $range]];
        }

        return $filters;
    }
}
