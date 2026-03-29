<?php

use App\Models\Product;
use App\ValueObjects\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{get, artisan};

uses(RefreshDatabase::class);

test('it filters products by exact price boundaries', function () {
    artisan('scout:delete-index', ['name' => (new Product())->searchableAs()]);

    Product::factory()->create(['title' => 'Cheap Item', 'price' => new Money(100)]);
    Product::factory()->create(['title' => 'Target Item', 'price' => new Money(500)]);
    Product::factory()->create(['title' => 'Expensive Item', 'price' => new Money(1000)]);

    Product::all()->each->searchable();

    /** @var \Tests\TestCase $this */
    $this->refreshIndex();

    get('/search?min_price=500&max_price=500')
        ->assertStatus(200)
        ->assertSee('Target Item')
        ->assertDontSee('Cheap Item')
        ->assertDontSee('Expensive Item');
});
