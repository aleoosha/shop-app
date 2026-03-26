<?php

declare(strict_types=1);

use App\Models\Product;
use App\ValueObjects\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('it casts price to Money object when retrieving from database', function () {
    // Arrange: Создаем запись напрямую в БД
    DB::table('products')->insert([
        'title' => 'Test Product',
        'price' => 1250.50,
        'description' => 'Description',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Act: Достаем модель через Eloquent
    $product = Product::first();

    // Assert: Проверяем, что это объект Money
    expect($product->price)->toBeInstanceOf(Money::class)
        ->and($product->price->amount)->toBe(1250.50)
        ->and($product->price->formatted())->toBe('1 250.50 ₽');
});

test('it serializes Money object back to float when saving', function () {
    // Arrange
    $product = new Product([
        'title' => 'New Product',
        'description' => 'Desc',
    ]);

    // Act: Устанавливаем цену как объект
    $product->price = new Money(500.00);
    $product->save();

    // Assert: Проверяем, что в базе лежит число
    assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 500.00
    ]);
});
