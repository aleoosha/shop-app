<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\InteractsWithElasticsearch;

uses(RefreshDatabase::class, InteractsWithElasticsearch::class);

beforeEach(fn () => $this->setUpElasticsearch());

test('it returns products via search api with correct structure', function () {
    $category = Category::factory()->create(['title' => 'Phones']);
    Product::factory()->create([
        'title' => 'iPhone 15 Pro',
        'price' => 100000,
        'category_id' => $category->id,
    ]);

    $this->refreshIndex();

    $response = $this->getJson('/api/products/search?q=iphone');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'price' => [
                        'decimal',
                        'formatted',
                        'currency',
                    ],
                    'category' => [
                        'id',
                        'title',
                    ],
                    'specs' => [
                        'brand',
                        'color',
                        'condition',
                        'country',
                    ],
                    'created_at',
                ],
            ],
            'meta', // если используешь пагинацию
            'links',
        ])
        ->assertJsonPath('data.0.price.decimal', 1000)
        ->assertJsonPath('data.0.price.formatted', '1 000.00 ₽');
});

test('it filters products by category via api', function () {
    $cat1 = Category::factory()->create();
    $cat2 = Category::factory()->create();

    Product::factory()->create(['category_id' => $cat1->id, 'title' => 'Correct']);
    Product::factory()->create(['category_id' => $cat2->id, 'title' => 'Wrong']);

    $this->refreshIndex();

    $response = $this->getJson("/api/products/search?category_id={$cat1->id}");

    $response->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Correct');
});
