<?php

declare(strict_types=1);

use App\Models\Product;
use App\ValueObjects\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('it casts price to Money object when retrieving from database', function () {
    DB::table('products')->insert([
        'title' => 'Test Product',
        'price' => 1250.50,
        'description' => 'Description',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $product = Product::first();

    expect($product->price)->toBeInstanceOf(Money::class)
        ->and($product->price->amount)->toBe(1250.50)
        ->and($product->price->formatted())->toBe('1 250.50 ₽');
});

test('it serializes Money object back to float when saving', function () {
    $product = new Product([
        'title' => 'New Product',
        'description' => 'Desc',
    ]);

    $product->price = new Money(500.00);
    $product->save();

    assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 500.00
    ]);
});

test('it handles null price', function () {
    $product = new Product(['title' => 'Null Product']);
    $product->price = null;
    $product->save();

    expect(Product::first()->price)->toBeNull();
});
