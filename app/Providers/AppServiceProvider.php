<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use App\Contracts\Services\LogServiceContract;
use App\Services\LogService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, GuzzleClient::class);
        $this->app->bind(LogServiceContract::class, LogService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
