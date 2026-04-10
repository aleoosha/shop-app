<?php

declare(strict_types=1);

namespace App\Console\Commands\System;

use App\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOldCartsCommand extends Command
{
    /**
     * Имя и сигнатура консольной команды.
     */
    protected $signature = 'app:cleanup-old-carts';

    /**
     * Описание команды.
     */
    protected $description = 'Физическое удаление из базы корзин, помеченных как SoftDeleted более недели назад';

    /**
     * Выполнение команды.
     */
    public function handle(): void
    {
        $this->info('Начало очистки старых корзин...');

        $count = 0;
        
        Cart::onlyTrashed()
            ->where('deleted_at', '<', now()->subWeek())
            ->chunkById(100, function ($carts) use (&$count) {
                foreach ($carts as $cart) {
                    $cart->forceDelete();
                    $count++;
                }
            });

        $this->info("Очистка завершена. Удалено корзин: {$count}");
        
        if ($count > 0) {
            Log::info("Система: Очищено старых корзин: {$count}");
        }
    }
}
