<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'Sony', 'Huawei', 'Google'];
        $colors = ['Black', 'White', 'Space Gray', 'Midnight', 'Blue', 'Red'];
        $conditions = ['new', 'used', 'refurbished'];
        $countries = ['China', 'USA', 'Vietnam', 'Korea', 'Germany'];

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(2, true),
            'price' => $this->faker->numberBetween(10000, 200000), 
            'category_id' => null,
            
            'specs' => [
                'brand' => $this->faker->randomElement($brands),
                'color' => $this->faker->randomElement($colors),
                'condition' => $this->faker->randomElement($conditions),
                'country' => $this->faker->randomElement($countries),
                'weight' => $this->faker->numberBetween(150, 500) . 'g',
            ],
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
