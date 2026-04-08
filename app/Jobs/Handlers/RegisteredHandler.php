<?php

declare(strict_types=1);

namespace App\Jobs\Handlers;

use App\Contracts\Jobs\OutboxHandlerContract;
use App\Contracts\Services\LogServiceContract;
use App\DTOs\Outbox\RegisteredEventData;
use App\Models\User;
use App\Services\CartService;
use Exception;

class RegisteredHandler implements OutboxHandlerContract
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly LogServiceContract $logService
    ) {}

    /**
     * Обработка события регистрации из Outbox.
     * 
     * @param array<string, mixed> $payload
     */
    public function handle(array $payload): void
    {
        try {
            $data = RegisteredEventData::from($payload);

            /** @var User|null $user */
            $user = User::find($data->userId);

            if (!$user) {
                $this->logService->error(
                    "RegisteredHandler: User not found", 
                    new Exception("User with ID {$data->userId} missing during outbox processing"),
                    ['payload' => $payload]
                );
                return;
            }

            if ($data->cartToken) {
                $this->cartService->mergeGuestCart(
                    $data->cartToken, 
                    (int) $user->id
                );

                $this->logService->action('cart_merged_after_registration', [
                    'user_id' => $user->id,
                    'token' => $data->cartToken
                ]);
            }

        } catch (Exception $e) {
            $this->logService->error("RegisteredHandler: Processing error", $e, [
                'payload' => $payload
            ]);
            
            throw $e;
        }
    }
}
