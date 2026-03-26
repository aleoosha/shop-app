<?php

declare(strict_types=1);

use App\Models\Product;
use function Pest\Laravel\getJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns suggestions for autocomplete', function () {
    // 1. Arrange: Создаем тестовые данные
    Product::factory()->create(['title' => 'iPhone 15 Pro']);
    Product::factory()->create(['title' => 'iPad Air']);
    Product::factory()->create(['title' => 'Samsung Galaxy']);

    // Ждем, пока Elastic проиндексирует (в тестах это мгновенно через Refresh)
    
    // 2. Act: Ищем по префиксу "iph"
    $response = getJson('/api/products/autocomplete?q=iph');

    // 3. Assert: Проверяем, что iPhone в списке, а Samsung — нет
    $response->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['title' => 'iPhone 15 Pro'])
        ->assertJsonMissing(['title' => 'Samsung Galaxy'])
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'url'] // Проверяем структуру каждого элемента
            ]
        ]);
});
