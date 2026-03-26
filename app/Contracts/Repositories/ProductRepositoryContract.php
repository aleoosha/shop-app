<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\ProductSearchDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryContract
{
    public function search(ProductSearchDTO $data): LengthAwarePaginator;
}