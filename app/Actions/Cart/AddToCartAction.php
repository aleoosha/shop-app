<?php

declare(strict_types=1);

namespace App\Actions\Cart;

use App\DTOs\Cart\CartItemDTO;
use App\DTOs\Cart\AddToCartResponseDTO;
use App\Models\Product;
use App\Services\CartService;
use App\Contracts\Services\LogServiceContract;
use App\Enums\LogChannel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AddToCartAction
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly LogServiceContract $logger,
        private readonly Guard $auth
    ) {}

    public function execute(CartItemDTO $data): AddToCartResponseDTO
    {
        $identifier = $this->auth->id() ?? request()->cookie('cart_token');
        $lockKey = "cart_lock_{$identifier}";

        return Cache::lock($lockKey, 5)->get(function () use ($data, $identifier) {
            return DB::transaction(function () use ($data, $identifier) {
                $cart = $this->cartService->getOrCreateCart();

                if (!$cart->exists) {
                    $cart->guest_id = request()->cookie('cart_token');
                    $cart->save();
                    
                    $this->logger->info("Created new cart for {$identifier}", [
                        'cart_id' => $cart->id,
                    ], LogChannel::SYSTEM);
                }

                $product = Product::query()
                    ->where('id', $data->productId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $item = $cart->items()
                    ->where('product_id', $data->productId)
                    ->lockForUpdate()
                    ->firstOrNew(['product_id' => $data->productId]);

                $item->forceFill([
                    'quantity' => ($item->quantity ?? 0) + $data->quantity,
                    'price_at_addition' => $product->price->amount,
                ])->save();

                $isAvailable = $product->stock >= $item->quantity;

                if (!$isAvailable) {
                    $this->logger->warning("Inventory alert: product out of stock", [
                        'product_id' => $product->id,
                        'cart_id' => $cart->id,
                        'requested_qty' => $item->quantity,
                        'available_stock' => $product->stock,
                    ], LogChannel::STORES);
                }

                $this->logger->info("Product added to cart", [
                    'user_id' => $this->auth->id(),
                    'product_id' => $product->id,
                    'quantity' => $data->quantity,
                ], LogChannel::SYSTEM);

                return new AddToCartResponseDTO(
                    cartCount: $this->cartService->getCartCount(),
                    cartTotal: $this->cartService->getCartTotal(),
                    productId: $product->id,
                    quantity: $item->quantity,
                    isAvailable: $isAvailable
                );
            });
        });
    }
}
