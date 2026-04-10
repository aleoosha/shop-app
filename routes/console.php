<?php

use App\Jobs\ProcessOutboxEvent;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune')->daily()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
Schedule::command('model:prune')->daily()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
Schedule::job(new ProcessOutboxEvent)->everyMinute();
Schedule::command('app:cleanup-old-carts')->dailyAt('03:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
