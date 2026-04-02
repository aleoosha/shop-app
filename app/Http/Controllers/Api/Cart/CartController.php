<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use App\Actions\Cart\AddToCartAction;
use App\DTOs\Cart\CartItemDTO;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class CartController extends Controller
{
    use ApiResponse;
    public function add(CartItemDTO $data, AddToCartAction $action): JsonResponse
    {
        $result = $action->execute($data);

        return $this->success($result, 'Product added to cart', 201);
    }
}
