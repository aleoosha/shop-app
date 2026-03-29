<?php

namespace App\Providers;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\Repositories\Elasticsearch\ProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryContract::class, 
            ProductRepository::class
        );
    }
}