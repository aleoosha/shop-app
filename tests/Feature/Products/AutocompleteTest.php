<?php

declare(strict_types=1);

use App\Models\Product;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('it returns suggestions for autocomplete', function () {
    Product::factory()->create(['title' => 'iPhone 15 Pro']);
    Product::factory()->create(['title' => 'iPad Air']);
    Product::factory()->create(['title' => 'Samsung Galaxy']);

    Product::all()->each->searchable();
    
    /** @var \Tests\TestCase $this */
    $this->refreshIndex();

    $response = getJson('/api/products/autocomplete?q=iph');

    $response->assertStatus(200)
        ->assertJsonPath('data.0.title', 'iPhone 15 Pro')
        ->assertJsonMissing(['title' => 'Samsung Galaxy'])
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'url']
            ]
        ]);
});
