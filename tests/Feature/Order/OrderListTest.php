<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Product;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns a paginated list of user orders', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Order::factory()->count(2)->create(['user_id' => $user->id]);
    
    Order::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)
        ->getJson('/api/orders');

    $response->assertOk()
        ->assertJsonCount(2, 'data.data')
        ->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['uuid', 'total_price', 'status', 'address', 'created_at', 'items_count']
                ],
                'meta',
                'links'
            ]
        ]);
});

it('unauthenticated user cannot access order list', function () {
    $this->getJson('/api/orders')->assertStatus(401);
});
