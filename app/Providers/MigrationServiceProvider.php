<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Регистрация путей к миграциям.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom([
            database_path('migrations/carts'),
            database_path('migrations/categories'),
            database_path('migrations/orders'),
            database_path('migrations/outbox-events'),
            database_path('migrations/products'),
            database_path('migrations/system'),
            database_path('migrations/users'),
        ]);
    }
}
