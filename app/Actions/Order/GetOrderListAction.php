<?php

declare(strict_types=1);

namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetOrderListAction
{
    public function __construct(
        protected Guard $auth
    ) {}

    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->where('user_id', $this->auth->id())
            ->with(['items'])
            ->latest()
            ->paginate($perPage);
    }
}
