<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к таблицам категорий и корзин.
     */
    public function up(): void
    {
        // Используем Schema::table БЕЗ метода ->change() для проблемных колонок
        Schema::table('categories', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор категории')->change();
        });

        // Для PostgreSQL добавляем комментарии напрямую через SQL
        // Это не затрагивает данные (NULL/NOT NULL) и не пытается пересоздать колонки
        if (DB::getDriverName() === 'pgsql') {
            // Таблица CATEGORIES
            DB::statement("COMMENT ON COLUMN categories.title IS 'Название категории'");
            DB::statement("COMMENT ON COLUMN categories.slug IS 'Уникальный URL-псевдоним'");
            DB::statement("COMMENT ON COLUMN categories.parent_id IS 'ID родительской категории'");
            DB::statement("COMMENT ON COLUMN categories._lft IS 'Левый индекс иерархии (Nested Set)'");
            DB::statement("COMMENT ON COLUMN categories._rgt IS 'Правый индекс иерархии (Nested Set)'");

            // Комментарии к самим таблицам
            DB::statement("COMMENT ON TABLE categories IS 'Иерархическая структура категорий товаров'");
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
