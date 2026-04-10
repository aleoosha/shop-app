<?php

declare(strict_types=1);

namespace App\Actions\Order;

use App\DTOs\Order\CheckoutDTO;
use App\DTOs\Order\OrderResponseDTO;
use App\Enums\OrderStatus;
use App\Jobs\ProcessOutboxEvent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutAction
{
    public function __construct(
        protected CartService $cartService,
        protected Guard $auth
    ) {}

    public function execute(CheckoutDTO $data): OrderResponseDTO
    {
        $cart = $this->cartService->getOrCreateCart();

        if (! $cart->exists || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Ваша корзина пуста.']);
        }

        return DB::transaction(function () use ($cart, $data) {
            $order = Order::create([
                'user_id' => $this->auth->id(),
                'delivery_address' => $data->address,
                'phone' => $data->phone,
                'note' => $data->note,
                'total_price' => $this->cartService->getCartTotal(),
                'status' => OrderStatus::PENDING,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product()->lockForUpdate()->first();

                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => ["Товара {$product->title} недостаточно на складе."],
                    ]);
                }

                $product->decrement('stock', $item->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'price_at_purchase' => $item->price_at_addition,
                    'title_at_purchase' => $product->title,
                    'quantity' => $item->quantity,
                ]);
            }

            DB::table('outbox_events')->insert([
                'event_type' => 'OrderCreated',
                'payload' => json_encode([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]),
                'created_at' => now(),
            ]);

            $this->cartService->clearCurrentCart();

            ProcessOutboxEvent::dispatch();

            return OrderResponseDTO::fromModel($order);
        });
    }
}
