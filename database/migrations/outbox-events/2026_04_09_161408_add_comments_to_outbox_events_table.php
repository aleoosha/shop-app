<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к таблице событий Outbox.
     */
    public function up(): void
    {
        Schema::table('outbox_events', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор события')->change();
            
            $table->string('event_type')
                ->comment('Полное имя класса события (например, App\Events\OrderCreated)')
                ->change();

            $table->jsonb('payload')
                ->comment('Данные события в формате JSONB для восстановления контекста воркером')
                ->change();

            $table->integer('attempts')
                ->default(0)
                ->comment('Количество предпринятых попыток обработки события воркером')
                ->change();

            $table->text('last_error')
                ->nullable()
                ->comment('Текст ошибки, возникшей при последней попытке обработки')
                ->change();

            $table->timestamp('processed_at')
                ->nullable()
                ->comment('Метка времени успешного завершения обработки события')
                ->change();

            $table->timestamp('failed_at')
                ->nullable()
                ->comment('Метка времени окончательного падения события после исчерпания попыток')
                ->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE outbox_events IS 'Реестр отложенных событий для обеспечения гарантированной доставки (Transactional Outbox)'");
        }
    }

    /**
     * Откат не требуется.
     */
    public function down(): void
    {
        //
    }
};
