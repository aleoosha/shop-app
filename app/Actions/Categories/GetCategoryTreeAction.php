<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetCategoryTreeAction
{
    public function handle(): Collection
    {
        return Cache::remember('categories_tree', 3600, function () {
            return Category::withDepth()->get();
        });
    }
}
