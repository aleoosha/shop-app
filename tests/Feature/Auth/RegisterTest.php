<?php

use App\Models\User;
use App\Jobs\ProcessOutboxEvent;
use Illuminate\Support\Facades\Bus;
use Illuminate\Auth\Events\Registered;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('registers a new user and dispatches outbox job', function () {
    Bus::fake();

    $payload = [
        'name' => 'Alexey',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'device_name' => 'Postman',
    ];

    $response = $this->postJson('/api/register', $payload, [
        'X-Idempotency-Key' => 'unique-registration-key'
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.user.email', 'test@example.com');

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

    $this->assertDatabaseHas('outbox_events', [
        'event_type' => Registered::class,
        'processed_at' => null,
    ]);

    Bus::assertDispatched(ProcessOutboxEvent::class);
});

it('prevents double registration with the same idempotency key', function () {
    $payload = [
        'name' => 'Alexey',
        'email' => 'duplicate@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'device_name' => 'Postman',
    ];

    $headers = ['X-Idempotency-Key' => 'fixed-key'];

    $this->postJson('/api/register', $payload, $headers)->assertStatus(201);
    
    $response = $this->postJson('/api/register', $payload, $headers);

    $response->assertStatus(200)
        ->assertHeader('X-Idempotency-Cache', 'HIT');

    expect(User::whereEmail('duplicate@example.com')->count())->toBe(1);
});
