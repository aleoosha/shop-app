<?php

namespace App\Providers;

use App\Contracts\Services\LogServiceContract;
use App\Jobs\Handlers\RegisteredHandler;
use App\Listeners\Cart\MergeCartAfterLogin;
use App\Services\CartService;
use App\Services\LogService;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;

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
        $this->app->singleton('outbox.map', fn () => [
            Registered::class => RegisteredHandler::class,
        ]);
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

        Event::listen(
            Registered::class,
            MergeCartAfterLogin::class
        );
    }
}
