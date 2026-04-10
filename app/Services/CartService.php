<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class CartService
{
    /**
     * Кэш для корзины в рамках одного запроса (Singleton-поведение).
     */
    private ?Cart $currentCart = null;

    public function __construct(
        private readonly Guard $auth,
        private readonly Request $request
    ) {}

    /**
     * Найти существующую корзину или создать новый объект (без сохранения в БД).
     */
    public function getOrCreateCart(): Cart
    {
        if ($this->currentCart) {
            return $this->currentCart;
        }

        if ($this->auth->check()) {
            $this->currentCart = Cart::firstOrCreate([
                'user_id' => $this->auth->id(),
            ]);

            return $this->currentCart;
        }

        $guestId = $this->request->cookie('cart_token');

        if ($guestId) {
            $this->currentCart = Cart::firstOrCreate([
                'guest_id' => $guestId,
            ]);

            return $this->currentCart;
        }

        return new Cart;
    }

    /**
     * Общее количество товаров (через коллекцию в памяти).
     */
    public function getCartCount(): int
    {
        $cart = $this->getOrCreateCart();

        if (! $cart->exists) {
            return 0;
        }

        return (int) $cart->items->sum('quantity');
    }

    /**
     * Общая стоимость (через коллекцию в памяти).
     */
    public function getCartTotal(): int
    {
        $cart = $this->getOrCreateCart();

        if (! $cart->exists) {
            return 0;
        }

        return (int) $cart->items->sum(function ($item) {
            return $item->quantity * $item->price_at_addition->amount;
        });
    }

    /**
     * Слияние гостевой корзины с основной при логине.
     */
    public function mergeGuestCart(string $guestId, int $userId): void
    {
        $guestCart = Cart::where('guest_id', $guestId)->first();
        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        if (! $guestCart || $guestCart->id === $userCart->id) {
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $userItem = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userItem) {
                $userItem->increment('quantity', $guestItem->quantity);
            } else {
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        $guestCart->delete();

        $this->currentCart = null;
    }

    /**
     * Полная очистка и удаление текущей корзины из БД и памяти.
     */
    public function clearCurrentCart(): void
    {
        $cart = $this->getOrCreateCart();

        if ($cart->exists) {
            $cart->items()->delete();
            $cart->delete();

            $this->currentCart = null;
        }
    }
}
