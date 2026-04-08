<?php

declare(strict_types=1);

namespace App\Console\Commands\Outbox;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RetryFailedOutboxEventsCommand extends Command
{
    protected $signature = 'outbox:retry {--id= : ID конкретного события}';
    protected $description = 'Возвращает бракованные Outbox события в очередь на обработку';

    public function handle(): int
    {
        $query = DB::table('outbox_events')->whereNotNull('failed_at');

        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('Бракованных событий не найдено.');
            return self::SUCCESS;
        }

        $query->update([
            'failed_at' => null,
            'attempts' => 0,
            'last_error' => null,
        ]);

        $this->info("Успешно восстановлено событий: {$count}.");
        
        return self::SUCCESS;
    }
}
