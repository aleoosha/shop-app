<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->update(['category_id' => null]);
        DB::table('categories')->delete();
        DB::statement("SELECT setval(pg_get_serial_sequence('categories', 'id'), 1, false)");

        $now = now();

        $electronicsId = DB::table('categories')->insertGetId([
            'title' => 'Электроника',
            'slug' => 'electronics',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $smartphonesId = DB::table('categories')->insertGetId([
            'title' => 'Смартфоны',
            'slug' => 'smartphones',
            'parent_id' => $electronicsId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('categories')->insert([
            [
                'title' => 'Apple', 
                'slug' => 'apple', 
                'parent_id' => $smartphonesId, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'title' => 'Samsung', 
                'slug' => 'samsung', 
                'parent_id' => $smartphonesId, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'title' => 'Ноутбуки', 
                'slug' => 'laptops', 
                'parent_id' => $electronicsId, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'title' => 'Одежда', 
                'slug' => 'clothing', 
                'parent_id' => null, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
        ]);

        Category::fixTree();

        $leafIds = Category::whereIsLeaf()->pluck('id');
        $products = Product::all();

        if ($leafIds->isNotEmpty() && $products->isNotEmpty()) {
            foreach ($products as $product) {
                $product->update(['category_id' => $leafIds->random()]);
            }
        }
    }
}
