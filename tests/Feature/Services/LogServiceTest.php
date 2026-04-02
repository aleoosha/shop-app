<?php

use App\Contracts\Services\LogServiceContract;
use Illuminate\Support\Facades\Log;
use App\Models\User;

test('it masks sensitive data in logs', function () {
    Log::shouldReceive('channel->info')
        ->once()
        ->with(
            'Test message', 
            Mockery::on(fn($context) => $context['password'] === '********')
        );

    $service = app(LogServiceContract::class);
    $service->info('Test message', [
        'user' => 'admin',
        'password' => 'secret123'
    ]);
});

test('it includes user id in action logs', function () {
    $user = User::factory()->create();
    
    /** @var \Tests\TestCase $this */
    $this->actingAs($user);

    Log::shouldReceive('channel')
        ->with('actions')
        ->andReturnSelf()
        ->shouldReceive('info')
        ->once()
        ->with(
            'Action: test_action',
            Mockery::on(function($context) use ($user) {
                return $context['user_id'] === $user->id 
                    && isset($context['ip']) 
                    && is_array($context['data']);
            })
        );

    app(LogServiceContract::class)->action('test_action');
});
