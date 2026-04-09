<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Получить соединение для Telescope.
     */
    public function getConnection(): ?string
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Добавление комментариев к таблицам мониторинга Telescope.
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());

        // 1. Таблица TELESCOPE_ENTRIES
        $schema->table('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence')->comment('Порядковый номер записи (Primary Key)')->change();
            $table->uuid('uuid')->comment('Уникальный идентификатор записи')->change();
            $table->uuid('batch_id')->comment('ID пачки записей (группировка в рамках одного запроса/процесса)')->change();
            $table->string('family_hash')->nullable()->comment('Хеш для группировки похожих событий (например, одинаковых исключений)')->change();
            $table->boolean('should_display_on_index')->comment('Флаг отображения записи в общем списке интерфейса')->change();
            $table->string('type', 20)->comment('Тип записи (request, command, query, log, и т.д.)')->change();
            $table->longText('content')->comment('JSON-данные с деталями записи')->change();
            $table->dateTime('created_at')->nullable()->comment('Время создания записи')->change();
        });

        // 2. Таблица TELESCOPE_ENTRIES_TAGS
        $schema->table('telescope_entries_tags', function (Blueprint $table) {
            $table->uuid('entry_uuid')->comment('Связь с основной записью Telescope')->change();
            $table->string('tag')->comment('Тег для фильтрации и поиска (например, ID пользователя или статус)') ->change();
        });

        // 3. Таблица TELESCOPE_MONITORING
        $schema->table('telescope_monitoring', function (Blueprint $table) {
            $table->string('tag')->comment('Теги, за которыми ведется активное наблюдение')->change();
        });

        // Комментарии к таблицам (PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE telescope_entries IS 'Основные записи мониторинга Telescope (запросы, команды, события)'");
            DB::statement("COMMENT ON TABLE telescope_entries_tags IS 'Теги для индексации и поиска по записям Telescope'");
            DB::statement("COMMENT ON TABLE telescope_monitoring IS 'Список тегов, поставленных на мониторинг'");
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
