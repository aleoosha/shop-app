<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к системным таблицам очередей.
     */
    public function up(): void
    {
        // 1. Таблица JOBS
        Schema::table('jobs', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор задачи')->change();
            $table->string('queue')->comment('Название очереди (high, default, scout)')->change();
            $table->longText('payload')->comment('Данные задачи в формате JSON')->change();
            $table->unsignedTinyInteger('attempts')->comment('Количество попыток выполнения')->change();
            $table->unsignedInteger('reserved_at')->comment('Время захвата задачи воркером')->change();
            $table->unsignedInteger('available_at')->comment('Время, когда задача станет доступна')->change();
            $table->unsignedInteger('created_at')->comment('Время постановки в очередь')->change();
        });

        // 2. Таблица JOB_BATCHES
        Schema::table('job_batches', function (Blueprint $table) {
            $table->string('id')->comment('Уникальный строковый ID пакета')->change();
            $table->string('name')->comment('Человекочитаемое имя группы задач')->change();
            $table->integer('total_jobs')->comment('Всего задач в пакете')->change();
            $table->integer('pending_jobs')->comment('Задач в ожидании')->change();
            $table->integer('failed_jobs')->comment('Задач, завершившихся ошибкой')->change();
            $table->longText('failed_job_ids')->comment('Список ID упавших задач')->change();
            $table->mediumText('options')->comment('Опции пакета (сериализовано)')->change();
            $table->integer('cancelled_at')->comment('Время отмены пакета')->change();
            $table->integer('created_at')->comment('Время создания пакета')->change();
            $table->integer('finished_at')->comment('Время завершения пакета')->change();
        });

        // 3. Таблица FAILED_JOBS
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->id()->comment('ID записи об ошибке')->change();
            $table->string('uuid')->comment('Уникальный UUID упавшей задачи')->change();
            $table->text('connection')->comment('Имя драйвера (redis/database)')->change();
            $table->text('queue')->comment('Имя очереди')->change();
            $table->longText('payload')->comment('Тело упавшей задачи')->change();
            $table->longText('exception')->comment('Текст ошибки и Stack Trace')->change();
            $table->timestamp('failed_at')->comment('Время окончательного падения')->change();
        });

        // Комментарии к самим таблицам (только для PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE jobs IS 'Очередь задач (Database driver)'");
            DB::statement("COMMENT ON TABLE job_batches IS 'Пакетная обработка задач'");
            DB::statement("COMMENT ON TABLE failed_jobs IS 'Реестр окончательно упавших задач'");
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
