<?php

declare(strict_types=1);

namespace App\Actions\Cart;

use App\DTOs\Cart\CartItemDTO;
use App\Models\Product;
use App\Services\CartService;
use App\Contracts\Services\LogServiceContract;

class AddToCartAction
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly LogServiceContract $logger
    ) {}

    public function execute(CartItemDTO $data): array
    {
        $cart = $this->cartService->getOrCreateCart();

        if (!$cart->exists) {
            $cart->guest_id = request()->cookie('cart_token');
            $cart->save();
        }

        $product = Product::select(['id', 'price', 'stock'])->findOrFail($data->productId);

        $item = $cart->items()->firstOrNew(['product_id' => $data->productId]);
        
        $item->forceFill([
            'quantity' => ($item->quantity ?? 0) + $data->quantity,
            'price_at_addition' => $product->price,
        ])->save();

        if ($product->stock < $item->quantity) {
            $this->logger->info("Product added to cart but out of stock locally", [
                'product_id' => $product->id,
                'requested' => $item->quantity,
                'available' => $product->stock,
            ]);
        }

        return [
            'cart_count' => $this->cartService->getCartCount(),
            'cart_total' => $this->cartService->getCartTotal(),
            'added_item' => [
                'product_id' => $product->id,
                'quantity' => $item->quantity,
                'is_available' => $product->stock >= $item->quantity,
            ]
        ];
    }
}
