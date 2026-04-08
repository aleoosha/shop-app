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
     * Общее количество товаров в корзине (сумма всех quantity).
     */
    public function getCartCount(): int
    {
        $cart = $this->getOrCreateCart();

        if (! $cart->exists) {
            return 0;
        }

        return (int) $cart->items()->sum('quantity');
    }

    /**
     * Общая стоимость корзины (сумма quantity * price_at_addition).
     */
    public function getCartTotal(): int
    {
        $cart = $this->getOrCreateCart();

        if (! $cart->exists) {
            return 0;
        }

        return (int) $cart->items()
            ->selectRaw('SUM(quantity * price_at_addition) as total')
            ->value('total') ?? 0;
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
}
