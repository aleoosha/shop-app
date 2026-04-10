<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use App\Casts\MoneyCast;
use App\Enums\OrderStatus;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property Money $total_price
 * @property OrderStatus $status
 */
class Order extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'delivery_address',
        'phone',
        'note',
        'total_price',
        'status',
    ];

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
