<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\Services\LogServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessOutboxEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;

    public $queue = 'high';

    public function __construct() {}

    public function handle(): void
    {
        $map = app('outbox.map');
        $logService = app(LogServiceContract::class);

        DB::transaction(function () use ($map, $logService) {
            $events = DB::table('outbox_events')
                ->whereNull('processed_at')
                ->whereNull('failed_at')
                ->orderBy('id')
                ->limit(50)
                ->lockForUpdate()
                ->get();

            foreach ($events as $event) {
                try {
                    $handlerClass = $map[$event->event_type] ?? null;
                    $payload = json_decode($event->payload, true);

                    if ($handlerClass) {
                        app($handlerClass)->handle($payload);
                    }

                    DB::table('outbox_events')->where('id', $event->id)->update([
                        'processed_at' => now(),
                        'attempts' => $event->attempts + 1,
                    ]);

                    $logService->action('outbox_processed', ['event' => $event->event_type]);

                } catch (Throwable $e) {
                    $attempts = $event->attempts + 1;
                    $shouldFail = $attempts >= 3;

                    DB::table('outbox_events')->where('id', $event->id)->update([
                        'attempts' => $attempts,
                        'last_error' => $e->getMessage(),
                        'failed_at' => $shouldFail ? now() : null,
                    ]);

                    $logService->error('Outbox task failed', $e, [
                        'event_id' => $event->id,
                        'attempts' => $attempts,
                    ]);
                }
            }
        });
    }
}
