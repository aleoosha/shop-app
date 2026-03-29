<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \App\Contracts\Repositories\ProductRepositoryContract;
use \App\Repositories\Elasticsearch\ProductRepository;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, GuzzleClient::class);
        $this->app->bind(ProductRepositoryContract::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
