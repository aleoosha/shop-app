<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'delivery_address' => $this->faker->address,
            'phone' => $this->faker->e164PhoneNumber,
            'note' => $this->faker->sentence,
            'status' => OrderStatus::PENDING,
            'total_price' => 0,
        ];
    }
}
