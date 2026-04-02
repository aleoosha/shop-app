<?php

declare(strict_types=1);

namespace App\Repositories\Elasticsearch;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\Search\ProductSearchDTO;
use App\Models\Product;
use App\ValueObjects\Money;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use \Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ProductRepository implements ProductRepositoryContract
{
    /**
     * Быстрый поиск для выпадающего списка (автокомплит).
     */
    public function autocomplete(string $query): Collection
    {
        return Product::searchQuery()
            ->query([
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'title' => [
                                    'query' => $query,
                                    'analyzer' => 'standard'
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

    /**
     * Основной полнотекстовый поиск с фильтрами.
     */
    public function search(ProductSearchDTO $data): LengthAwarePaginator
    {
        $boolQuery = Query::bool();

        if ($data->query) {
            $boolQuery->must(
                Query::multiMatch()
                    ->query($data->query)
                    ->fields([
                        'title.raw^10', 
                        'title^5', 
                        'description'
                    ])
                    ->analyzer('standard')
                    ->type('best_fields')
                    ->tieBreaker(0.0)
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

            if ($data->minPrice !== null) {
                $range['gte'] = Money::fromDecimal((float) $data->minPrice)->amount;
            }
            
            if ($data->maxPrice !== null) {
                $range['lte'] = Money::fromDecimal((float) $data->maxPrice)->amount;
            }
            
            $boolQuery->filter(['range' => ['price' => $range]]);
        }

        $results = Product::searchQuery($boolQuery)
            ->searchType('dfs_query_then_fetch')
            ->highlight('description')
            ->sort(
                $data->sortField === 'title' ? 'title.keyword' : $data->sortField, 
                $data->sortOrder
            )
            ->paginate($data->perPage);


        $hits = $results->getCollection();

        if ($hits->isNotEmpty()) {
            $models = new EloquentCollection(
                $hits->map(fn($hit) => $hit->model())
            );

            if ($models->isNotEmpty()) {
                (new EloquentCollection($models))->load(['category']);
            }
        }

        return $results;
    }
}
