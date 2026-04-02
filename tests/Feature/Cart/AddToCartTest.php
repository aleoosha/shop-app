<?php

use App\Models\Product;
use App\Actions\Cart\AddToCartAction;
use App\DTOs\Cart\CartItemDTO;
use App\Models\CartItem;
use Illuminate\Support\Str;

test('guest can add product to cart and get a guest_id cookie', function () {
    $product = Product::factory()->create(['price' => 100000]);

    /** @var \Tests\TestCase $this */
    $response = $this->postJson(route('cart.add'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response->assertStatus(201);
    $response->assertCookie('cart_token');

    $guestId = $response->getCookie('cart_token', false)->getValue(); 

    $this->assertDatabaseHas('carts', [
        'guest_id' => $guestId,
    ]);
});

test('prices are snapshotted and not changed when product price updates', function () {
    $product = Product::factory()->create(['price' => 50000]);
    
    $action = app(AddToCartAction::class);
    
    $guestId = (string) Str::uuid();
    request()->cookies->add(['cart_token' => $guestId]);

    $action->execute(CartItemDTO::from([
        'productId' => $product->id,
        'quantity' => 1,
    ]));

    $product->update(['price' => 99999]);

    $cartItem = CartItem::first();
    
    expect($cartItem->price_at_addition->amount)->toBe(50000);
});
