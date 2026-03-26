<?php

declare(strict_types=1);

namespace Tests\Feature\Products;

use App\Contracts\Repositories\ProductRepositoryContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;

test('search page returns successful response and calls repository', function () {
    // 1. Создаем "заглушку" для репозитория
    $this->mock(ProductRepositoryContract::class, function (MockInterface $mock) {
        $mock->shouldReceive('search')
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 15));
    });

    // 2. Делаем запрос к контроллеру
    $response = $this->get('/search?q=test&min_price=100');

    // 3. Проверяем статус и вьюху
    $response->assertStatus(200);
    $response->assertViewIs('products.index');
});