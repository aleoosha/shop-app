<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\DTOs\Search\ProductSearchDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchProductsAction
{
    public function __construct(
        private ProductRepositoryContract $repository
    ) {}

    public function handle(ProductSearchDTO $dto): LengthAwarePaginator
    {
        return $this->repository->search($dto);
    }
}
