<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\ProductSearchDTO;

class SearchProductsAction
{
    public function __construct(
        private ProductRepositoryContract $repository
    ) {}

    public function handle(ProductSearchDTO $dto): \Illuminate\Pagination\LengthAwarePaginator
    {
        $results = $this->repository->search($dto);

        // Паттерн "Трансформация данных"
        $results->getCollection()->transform(function ($hit) {
            return new \App\ViewModels\Web\Products\ProductSearchViewModel($hit);
        });

        return $results->withQueryString();
    }
}
