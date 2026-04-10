<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property \App\ValueObjects\Money $total_price
 * @property OrderStatus $status
 */
class Order extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'total_price',
        'status',
    ];

    /**
     * Автоматическая генерация UUID при создании заказа.
     */
    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function (Order $order) {
            $order->uuid = (string) Str::uuid();
        });
    }

    /**
     * Типизация полей.
     */
    protected function casts(): array
    {
        return [
            'total_price' => MoneyCast::class,
            'status' => OrderStatus::class,
        ];
    }

    /**
     * Связь с пользователем.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с составом заказа.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
