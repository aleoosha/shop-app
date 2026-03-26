<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class MoneyCast implements CastsAttributes
{
    /**
     * Преобразует значение ИЗ базы (число) В объект Money.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        return new Money((float) $value);
    }

    /**
     * Преобразует объект Money обратно В число для записи в базу.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): float
    {
        if ($value instanceof Money) {
            return $value->amount;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        throw new InvalidArgumentException('Цена должна быть числом или объектом Money.');
    }
}
