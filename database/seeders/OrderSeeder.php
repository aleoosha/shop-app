<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        User::flushEventListeners();
        Product::flushEventListeners();

        $users = User::query()->get();
        $products = Product::query()->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Заказы не созданы: нет пользователей или товаров в базе.');

            return;
        }

        foreach ($users as $user) {
            Order::factory()
                ->count(2)
                ->create(['user_id' => $user->id])
                ->each(function (Order $order) use ($products) {

                    $items = $products->random(2);
                    $totalAmount = 0;

                    foreach ($items as $product) {
                        $qty = rand(1, 2);
                        $price = $product->price->amount;

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price_at_purchase' => $price,
                            'title_at_purchase' => $product->title,
                        ]);

                        $totalAmount += $price * $qty;
                    }

                    $order->update(['total_price' => $totalAmount]);
                });
        }

        $this->command->info('Заказы успешно созданы для всех пользователей.');
    }
}
