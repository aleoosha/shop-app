<?php

declare(strict_types=1);

use App\Models\Product;
use App\ValueObjects\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('it casts price to Money object when retrieving from database', function () {
    DB::table('products')->insert([
        'title' => 'Test Product',
        'price' => 125050,
        'description' => 'Description',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $product = Product::first();

    expect($product->price)->toBeInstanceOf(Money::class)
        ->and($product->price->amount)->toBe(125050)
        ->and($product->price->formatted())->toBe('1 250.50 ₽');
});

test('it serializes Money object back to integer when saving', function () {
    $product = new Product([
        'title' => 'New Product',
        'description' => 'Desc',
    ]);

    $product->price = new Money(50000); 
    $product->save();

    assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 50000
    ]);
});

