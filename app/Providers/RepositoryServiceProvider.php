<?php

namespace App\Providers;

use App\Contracts\Repositories\ProductRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Repositories\Elasticsearch\ProductRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryContract::class,
            ProductRepository::class
        );

        $this->app->bind(
            UserRepositoryContract::class,
            UserRepository::class
        );
    }
}
