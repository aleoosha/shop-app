<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{get, artisan};

uses(RefreshDatabase::class);

test('it ranks title matches higher than description matches', function () {
    artisan('scout:delete-index', ['name' => (new Product())->searchableAs()]);   

    $weakMatch = Product::factory()->create([
        'title' => 'Обычный девайс',
        'description' => 'В описании этого товара есть секретное слово apple'
    ]);

    $strongMatch = Product::factory()->create([
        'title' => 'Смартфон Apple iPhone 15',
        'description' => 'Просто описание'
    ]);

    $weakMatch->searchable();
    $strongMatch->searchable();

    /** @var \Tests\TestCase $this */
    $this->refreshIndex();

    get('/search?q=apple')
        ->assertStatus(200)
        ->assertSeeInOrder([
            'Смартфон Apple iPhone 15', 
            'Обычный девайс'
        ]);
});
