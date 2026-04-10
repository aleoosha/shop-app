<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Этот метод Laravel вызовет автоматически при создании нового экземпляра модели.
     */
    public function initializeHasUuid(): void
    {
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Использовать UUID в маршрутах по умолчанию вместо ID.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
