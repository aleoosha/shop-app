<?php

namespace Database\Factories;

use App\Models\Product;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    return [
        'title'       => fake()->words(3, true),       // 3 случайных слова
        'price'       => new Money(fake()->randomFloat(2, 100, 10000)), // Цена от 100 до 10 000.00
        'description' => fake()->realText(200),        // Осмысленный текст на ~200 знаков
    ];
    }
}
