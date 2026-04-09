<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к таблице категорий.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор категории')->change();
            $table->string('title')->comment('Название категории')->change();
            $table->string('slug')->comment('Уникальный URL-псевдоним для маршрутизации')->change();

            // Поля паттерна Nested Set
            $table->unsignedBigInteger('parent_id')->nullable()->comment('ID родительской категории')->change();
            $table->unsignedInteger('_lft')->comment('Левый индекс в иерархии дерева (Nested Set)')->change();
            $table->unsignedInteger('_rgt')->comment('Правый индекс в иерархии дерева (Nested Set)')->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE categories IS 'Иерархическая структура категорий товаров (Nested Set)'");
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
