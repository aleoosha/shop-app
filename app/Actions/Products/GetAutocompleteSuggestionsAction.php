<?php

namespace App\Actions\Products;

use App\Contracts\Repositories\ProductRepositoryContract;

class GetAutocompleteSuggestionsAction
{
    public function __construct(private ProductRepositoryContract $repository) {}

    public function handle(?string $query): \Illuminate\Support\Collection
    {
        if (empty($query) || mb_strlen($query) < 2) {
            return collect();
        }

        return $this->repository->autocomplete($query);
    }
}
