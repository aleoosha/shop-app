<?php

declare(strict_types=1);

namespace App\Repositories\Elasticsearch;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\ProductSearchDTO;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Elastic\ScoutDriverPlus\Support\Query;

class ProductRepository implements ProductRepositoryContract
{
public function autocomplete(string $query): \Illuminate\Support\Collection
{
    return Product::searchQuery()
        ->query([
            'bool' => [
                'must' => [
                    [
                        'match_phrase_prefix' => [
                            'title' => [
                                'query' => $query,
                            ]
                        ]
                    ]
                ],
                'should' => [
                    [
                        'prefix' => [
                            'title' => [
                                'value' => strtolower($query),
                                'boost' => 20
                            ]
                        ]
                    ]
                ]
            ]
        ])
        ->size(10)
        ->execute()
        ->models();
}

    public function search(ProductSearchDTO $data): LengthAwarePaginator
    {
        $boolQuery = Query::bool();

        if ($data->query) {
            $boolQuery->must(
                Query::bool()
                    ->should(
                        Query::multiMatch()
                            ->query($data->query)
                            ->fields(['title^10', 'description'])
                            ->operator('and')
                    )
                    ->should(
                        Query::multiMatch()
                            ->query($data->query)
                            ->fields(['title^10', 'description'])
                            ->fuzziness('AUTO')
                    )
                    ->minimumShouldMatch(1)
            );
        } else {
            $boolQuery->must(Query::matchAll());
        }

        if ($data->categoryId !== null) {
            $boolQuery->filter([
                'term' => [
                    'category_id' => (int) $data->categoryId
                ]
            ]);
        }

        if ($data->minPrice !== null || $data->maxPrice !== null) {
            $range = [];
            if ($data->minPrice !== null) $range['gte'] = $data->minPrice;
            if ($data->maxPrice !== null) $range['lte'] = $data->maxPrice;
            $boolQuery->filter(['range' => ['price' => $range]]);
        }

        return Product::searchQuery($boolQuery)
            ->highlight('description')
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

        if ($data->categoryId !== null) {
            $filters[] = ['term' => ['category_id' => $data->categoryId]];
        }

        return $filters;
    }
}
