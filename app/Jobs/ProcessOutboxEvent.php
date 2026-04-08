<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessOutboxEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;

    public function __construct()
    {
        $this->afterCommit = true;
        $this->onQueue('high');
    }

    public function handle(): void
    {
        $map = app('outbox.map');

        DB::transaction(function () use ($map) {
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
                    if ($handlerClass) {
                        app($handlerClass)->handle(json_decode($event->payload, true));
                    }
                    DB::table('outbox_events')->where('id', $event->id)->update(['processed_at' => now()]);
                } catch (\Throwable $e) {
                    DB::table('outbox_events')->where('id', $event->id)->update([
                        'attempts' => $event->attempts + 1,
                        'last_error' => $e->getMessage(),
                        'failed_at' => ($event->attempts + 1 >= 3) ? now() : null,
                    ]);
                }
            }
        });
    }
}
