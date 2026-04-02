<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('it returns recursive category tree', function () {
    $parent = Category::factory()->create(['title' => 'Electronics']);
    $child = Category::factory()->create(['title' => 'Phones', 'parent_id' => $parent->id]);

    $response = $this->getJson('/api/products/category-tree');

    $response->assertStatus(200)
        ->assertJsonPath('data.0.title', 'Electronics')
        ->assertJsonPath('data.0.children.0.title', 'Phones');
});

test('it caches the category tree', function () {
    Category::factory()->create(['title' => 'Cached Category']);

    $this->getJson('/api/products/category-tree');
    
    Category::query()->delete();

    $response = $this->getJson('/api/products/category-tree');
    
    $response->assertStatus(200)
        ->assertJsonPath('data.0.title', 'Cached Category');
});
