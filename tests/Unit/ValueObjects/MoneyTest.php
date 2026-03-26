<?php

declare(strict_types=1);

use App\ValueObjects\Money;

test('it can format amount correctly', function () {
    $money = new Money(8284.92);
    expect($money->formatted())->toBe('8 284.92 ₽');
});

test('it throws exception for negative amount', function () {
    new Money(-100);
})->throws(InvalidArgumentException::class, 'Цена не может быть отрицательной');

test('it converts to string automatically', function () {
    $money = new Money(100);
    expect((string) $money)->toBe('100.00 ₽');
});
