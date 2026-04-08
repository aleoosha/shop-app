<?php

declare(strict_types=1);

namespace App\Contracts\Jobs;

/**
 * Интерфейс для всех обработчиков событий из таблицы Outbox.
 */
interface OutboxHandlerContract
{
    /**
     * Выполнить логику обработки события.
     *
     * @param array<string, mixed> $payload
     */
    public function handle(array $payload): void;
}
