<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->update(['category_id' => null]);

        Category::rebuildTree([
            [
                'title' => 'Электроника',
                'slug' => 'electronics',
                'children' => [
                    [
                        'title' => 'Смартфоны',
                        'slug' => 'smartphones',
                        'children' => [
                            ['title' => 'Apple', 'slug' => 'apple'],
                            ['title' => 'Samsung', 'slug' => 'samsung'],
                        ],
                    ],
                    [
                        'title' => 'Ноутбуки',
                        'slug' => 'laptops',
                    ],
                ],
            ],
            [
                'title' => 'Одежда',
                'slug' => 'clothing',
            ],
        ]);

        $leafIds = Category::whereIsLeaf()->pluck('id');

        if ($leafIds->isNotEmpty()) {
            Product::query()->chunkById(100, function ($products) use ($leafIds) {
                foreach ($products as $product) {
                    $product->update(['category_id' => $leafIds->random()]);
                }
            });
        }
    }
}
