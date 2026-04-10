<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Order;

use App\Actions\Order\GetOrderListAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    use ApiResponse;

    public function index(GetOrderListAction $action): JsonResponse
    {
        $orders = $action->execute();

        return $this->success(
            message: 'Список заказов получен',
            data: OrderResource::collection($orders)->response()->getData(true)
        );
    }
}
