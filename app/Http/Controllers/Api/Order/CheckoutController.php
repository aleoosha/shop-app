<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Actions\Order\CheckoutAction;
use App\DTOs\Order\CheckoutDTO;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    use ApiResponse;

    /**
     * Оформление заказа.
     * 
     * @param CheckoutDTO $dto Провалидированные данные (адрес, телефон)
     * @param CheckoutAction $action Бизнес-логика создания заказа
     */
    public function __invoke(
        CheckoutDTO $dto, 
        CheckoutAction $action
    ): JsonResponse {
        $orderData = $action->execute($dto);

        return $this->success(
            data: $orderData,
            message: 'Заказ успешно оформлен',
            code: 201
        );
    }
}
