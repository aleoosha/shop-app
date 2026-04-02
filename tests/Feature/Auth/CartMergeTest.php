<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Events\Login;

uses(RefreshDatabase::class);

test('it merges guest cart with user cart after login', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100000]);

    $guestId = 'test-guest-uuid';
    
    $guestCart = Cart::create(['guest_id' => $guestId]);
    
    CartItem::create([
        'cart_id' => $guestCart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_addition' => 100000
    ]);

    $this->withCookie('cart_token', $guestId);
    request()->cookies->add(['cart_token' => $guestId]);

    event(new Login('web', $user, false));

    $userCart = Cart::where('user_id', $user->id)->first();
    
    expect($userCart)->not->toBeNull('Корзина не была создана или привязана к пользователю');
    expect($userCart->items)->toHaveCount(1);
    expect($userCart->items->first()->product_id)->toBe($product->id);
    
    expect(Cart::where('guest_id', $guestId)->exists())->toBeFalse();
});
