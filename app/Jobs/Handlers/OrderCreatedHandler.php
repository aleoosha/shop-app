<?php

declare(strict_types=1);

namespace App\Jobs\Handlers;

use App\Contracts\Jobs\OutboxHandlerContract;
use App\Contracts\Services\LogServiceContract;
use App\Enums\LogChannel;
use App\Models\Order;

class OrderCreatedHandler implements OutboxHandlerContract
{
    public function __construct(
        private readonly LogServiceContract $logService
    ) {}

    public function handle(array $payload): void
    {
        $orderId = $payload['order_id'];
        $order = Order::with('user')->find($orderId);

        if (!$order) {
            return;
        }

        $this->logService->info(
            message: "Заказ оформлен: #{$order->id} (UUID: {$order->uuid})",
            context: [
                'user_id' => $order->user_id,
                'total_price' => $order->total_price->amount,
            ],
            channel: LogChannel::SYSTEM
        );

    }
}
