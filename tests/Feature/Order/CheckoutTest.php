<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Cache;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
    $this->product = Product::factory()->create([
        'title' => 'Test Phone',
        'price' => 100000, // 1000.00
        'stock' => 10
    ]);
});

it('successfully creates an order from cart', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $this->product->id,
        'quantity' => 2,
        'price_at_addition' => 100000
    ]);

    $payload = [
        'address' => 'Tomsk, Lenin str. 1',
        'phone' => '+79991234567',
        'note' => 'Ring bell'
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/orders/checkout', $payload, [
            'X-Idempotency-Key' => 'checkout-key-1'
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', 'pending');

    $this->assertDatabaseHas('orders', [
        'user_id' => $this->user->id,
        'total_price' => 200000,
        'delivery_address' => 'Tomsk, Lenin str. 1'
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $this->product->id,
        'price_at_purchase' => 100000,
        'title_at_purchase' => 'Test Phone',
        'quantity' => 2
    ]);

    $this->assertSoftDeleted('carts', ['id' => $cart->id]);
    $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);

    $this->assertDatabaseHas('outbox_events', [
        'event_type' => 'OrderCreated'
    ]);
});

it('fails to checkout with empty cart', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/orders/checkout', [
            'address' => 'Some address',
            'phone' => '+79991234567'
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['cart']);
});

it('preserves price snapshot even if product price changes later', function () {
    $cart = Cart::factory()->create(['user_id' => $this->user->id]);
    
    DB::table('cart_items')->insert([
        'cart_id' => $cart->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price_at_addition' => 100000,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->product->update(['price' => 500000]);

    $this->actingAs($this->user)->postJson('/api/orders/checkout', [
        'address' => 'Test Address',
        'phone' => '+79991112233'
    ])->assertStatus(201);

    $orderItem = \App\Models\OrderItem::first();
    
    // Сравниваем именно сумму в копейках
    expect($orderItem->price_at_purchase->amount)->toBe(100000);
});
