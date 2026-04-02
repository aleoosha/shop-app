<?php

declare(strict_types=1);

namespace Tests\Feature\Cart;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\InteractsWithElasticsearch;

/** @var \Tests\TestCase|\Tests\Traits\InteractsWithElasticsearch $this */

uses(RefreshDatabase::class, InteractsWithElasticsearch::class);

beforeEach(function () {
    $this->setUpElasticsearch();
});

test('it increments quantity if same product added twice', function () {
    $product = Product::factory()->create();
    
    $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 2]);

    $this->assertDatabaseCount('cart_items', 1);
    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 3
    ]);
});

test('guest cart token middleware assigns a token', function () {
    Product::factory()->create();
    $this->setUpElasticsearch();
    $this->refreshIndex();

    $response = $this->getJson('/api/products/search');

    $response->assertStatus(200);

    $response->assertCookie('cart_token'); 
});
