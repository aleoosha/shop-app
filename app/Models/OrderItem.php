<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property \App\ValueObjects\Money $price_at_purchase
 * @property string $title_at_purchase
 * @property int $quantity
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'price_at_purchase',
        'title_at_purchase',
        'quantity',
    ];

    /**
     * Типизация цен.
     */
    protected function casts(): array
    {
        return [
            'price_at_purchase' => MoneyCast::class,
        ];
    }

    /**
     * Обратная связь с заказом.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Связь с товаром (может вернуть null, если товар удален).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
