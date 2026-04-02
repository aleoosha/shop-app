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
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if (is_null($value)) {
            return null;
        }

        return new Money((int) $value);
    }

    /**
     * Преобразует объект Money обратно В число для записи в базу.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?int
    {
        if (is_null($value)) return null;

        if ($value instanceof Money) {
            return $value->amount;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value * 100);
        }

        throw new InvalidArgumentException('Цена должна быть числом или объектом Money.');
    }
}
