<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\ProductSearchDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ViewModels\Web\Products\ProductSearchViewModel;

class SearchProductsAction
{
    public function __construct(
        private ProductRepositoryContract $repository
    ) {}

    public function handle(ProductSearchDTO $dto): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator $results */
        $results = $this->repository->search($dto);

        $results->getCollection()->transform(function ($hit) {
            return new ProductSearchViewModel($hit);
        });

        return $results->withQueryString();
    }
}
