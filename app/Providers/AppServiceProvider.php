<?php

namespace App\Providers;

use \Illuminate\Contracts\Auth\Guard;
use \Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use App\Contracts\Services\LogServiceContract;
use App\Services\LogService;
use App\Services\CartService;
use App\Listeners\Cart\MergeCartAfterLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, GuzzleClient::class);
        $this->app->bind(LogServiceContract::class, LogService::class);
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService(
                $app->make(Guard::class),
                $app->make(Request::class)
        );
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            Login::class,
            MergeCartAfterLogin::class
        );
    }
}
