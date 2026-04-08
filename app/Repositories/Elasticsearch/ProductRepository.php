<?php

declare(strict_types=1);

namespace App\Repositories\Elasticsearch;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\Search\ProductSearchDTO;
use App\Models\Product;
use App\ValueObjects\Money;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryContract
{
    public function search(ProductSearchDTO $data): LengthAwarePaginator
    {
        $boolQuery = Query::bool();

        $this->applyFullTextSearch($boolQuery, $data->query);
        $this->applyCategoryFilter($boolQuery, $data->categoryId);
        $this->applyPriceFilter($boolQuery, $data->minPrice, $data->maxPrice);

        $this->applyTermFilter($boolQuery, 'brand', $data->brand);
        $this->applyTermFilter($boolQuery, 'color', $data->color);
        $this->applyTermFilter($boolQuery, 'condition', $data->condition);
        $this->applyTermFilter($boolQuery, 'country', $data->condition);

        $results = Product::searchQuery($boolQuery)
            ->searchType('dfs_query_then_fetch')
            ->highlight('description')
            ->sort($data->getElasticSortField(), $data->sortOrder)
            ->paginate($data->perPage);

        $this->loadRelations($results);

        return $results;
    }

    /**
     * Универсальный фильтр для точного совпадения (keyword).
     */
    private function applyTermFilter($boolQuery, string $field, ?string $value): void
    {
        if ($value) {
            $boolQuery->filter(['term' => [$field => $value]]);
        }
    }

    /**
     * Полнотекстовый поиск с весами.
     */
    private function applyFullTextSearch($boolQuery, ?string $queryString): void
    {
        if (! $queryString) {
            $boolQuery->must(Query::matchAll());

            return;
        }

        $boolQuery->must(
            Query::multiMatch()
                ->query($queryString)
                ->fields(['title.raw^10', 'title^5', 'description'])
                ->analyzer('standard')
                ->type('best_fields')
                ->tieBreaker(0.0)
        );
    }

    /**
     * Фильтрация по категории.
     */
    private function applyCategoryFilter($boolQuery, ?int $categoryId): void
    {
        if ($categoryId) {
            $boolQuery->filter(['term' => ['category_id' => $categoryId]]);
        }
    }

    /**
     * Фильтрация по диапазону цен.
     */
    private function applyPriceFilter($boolQuery, ?float $min, ?float $max): void
    {
        if (! $min && ! $max) {
            return;
        }

        $range = [];
        if ($min) {
            $range['gte'] = Money::fromDecimal($min)->amount;
        }
        if ($max) {
            $range['lte'] = Money::fromDecimal($max)->amount;
        }

        $boolQuery->filter(['range' => ['price' => $range]]);
    }

    /**
     * Жадная загрузка связей (Eager Loading).
     */
    private function loadRelations(LengthAwarePaginator $results): void
    {
        $hits = $results->getCollection();

        if ($hits->isEmpty()) {
            return;
        }

        $models = $hits->map(fn ($hit) => $hit->model())->filter();

        if ($models->isNotEmpty()) {
            (new EloquentCollection($models))->load(['category']);
        }
    }

    public function autocomplete(string $query): Collection
    {
        return Product::searchQuery()
            ->query([
                'bool' => [
                    'must' => [['match' => ['title' => ['query' => $query, 'analyzer' => 'standard']]]],
                    'should' => [['prefix' => ['title' => ['value' => strtolower($query), 'boost' => 20]]]],
                ],
            ])
            ->size(10)
            ->execute()
            ->models();
    }
}
