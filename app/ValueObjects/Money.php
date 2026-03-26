<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

readonly class Money
{
    public function __construct(
        public float $amount,
        public string $currency = 'RUB'
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Цена не может быть отрицательной');
        }
    }

    public function formatted(): string
    {
        return number_format($this->amount, 2, '.', ' ') . ' ₽';
    }

    public function __toString(): string
    {
        return $this->formatted();
    }
}
