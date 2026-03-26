<?php

declare(strict_types=1);

namespace Tests\Feature\Products;

use App\Contracts\Repositories\ProductRepositoryContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;
use function Pest\Laravel\mock;

uses(RefreshDatabase::class);

test('search page returns successful response and calls repository', function () {
    mock(ProductRepositoryContract::class)
        ->shouldReceive('search')
        ->once()
        ->andReturn(new LengthAwarePaginator(collect(), 0, 15));

    get('/search?q=test')
        ->assertStatus(200)
        ->assertViewIs('products.index')
        ->assertViewHas('products');
});