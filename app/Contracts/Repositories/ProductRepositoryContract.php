<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\Search\ProductSearchDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryContract
{
    public function search(ProductSearchDTO $data): LengthAwarePaginator;
    public function autocomplete(string $query): Collection;

}