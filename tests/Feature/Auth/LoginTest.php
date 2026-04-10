<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('authenticates user with correct credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
        'device_name' => 'test-device',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['accessToken']]);
});

it('blocks concurrent login attempts for the same email', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $lockKey = 'login_lock_'.md5($user->email);
    Cache::lock($lockKey, 10)->acquire();

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
        'device_name' => 'test-device',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.email.0'))->toBe('Слишком много попыток входа.');
});
