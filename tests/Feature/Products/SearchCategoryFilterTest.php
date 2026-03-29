<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Product;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it filters products by category in search results', function () {
    $electronics = Category::factory()->create(['title' => 'Электроника', 'slug' => 'electronics']);
    $clothing = Category::factory()->create(['title' => 'Одежда', 'slug' => 'clothing']);
    
    Product::factory()->create(['title' => 'iPhone 15','category_id' => $electronics->id]);
    Product::factory()->create(['title' => 'T-Shirt','category_id' => $clothing->id]);

    $products = Product::with('category')->get();
    $products->searchable();
    
    /** @var \Tests\TestCase $this */
    $this->refreshIndex();

    $response = get("/search?category_id={$electronics->id}");

    $response->assertStatus(200)
        ->assertSee('iPhone 15')
        ->assertDontSee('T-Shirt');
});
