<?php

declare(strict_types=1);

namespace App\Listeners\Cart;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class MergeCartAfterLogin
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly Request $request
    ) {}

    public function handle(Login $event): void
    {
        $guestId = $this->request->cookie('cart_token');

        if ($guestId) {
            $this->cartService
                ->mergeGuestCart($guestId, (int) $event->user->getAuthIdentifier());
        }
    }
}
