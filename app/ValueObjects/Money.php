<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

readonly class Money
{
    public function __construct(
        public int $amount, 
        public string $currency = 'RUB'
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Цена не может быть отрицательной');
        }
    }

    /**
     * Создать объект из человеческого формата (например, 100.50)
     */
    public static function fromFloat(float $amount, string $currency = 'RUB'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    /**
     * Получить сумму в обычном виде (например, 100.50)
     */
    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public static function fromDecimal(float $amount): ?self
    {
        if (is_null($amount)) {
            return null;
        }

        return new self((int) round($amount * 100));
    }

    public function formatted(): string
    {
        return number_format($this->toFloat(), 2, '.', ' ') . ' ₽';
    }

    public function __toString(): string
    {
        return $this->formatted();
    }
}
