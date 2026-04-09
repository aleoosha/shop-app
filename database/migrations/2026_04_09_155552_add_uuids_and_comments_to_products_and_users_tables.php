<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Обновляем USERS (здесь можно через Schema, так как нет сгенерированных колонок)
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->unique()->nullable();
            // Комментарии для обычных колонок
            $table->string('name')->comment('Имя пользователя')->change();
            $table->string('email')->comment('Электронная почта (логин)')->change();
        });

        // 2. Обновляем PRODUCTS (БЕЗ использования change())
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->unique()->nullable();
        });

        // 3. Добавляем комментарии через прямой SQL (безопасно для Postgres)
        if (DB::getDriverName() === 'pgsql') {
            // Комментарии к таблицам
            DB::statement("COMMENT ON TABLE users IS 'Таблица учетных записей пользователей'");
            DB::statement("COMMENT ON TABLE products IS 'Каталог товаров с виртуальными колонками характеристик'");

            // Комментарии к колонкам PRODUCTS (включая те, что вызвали ошибку)
            DB::statement("COMMENT ON COLUMN products.id IS 'Внутренний идентификатор товара'");
            DB::statement("COMMENT ON COLUMN products.title IS 'Название товара'");
            DB::statement("COMMENT ON COLUMN products.price IS 'Цена товара (decimal)'");
            DB::statement("COMMENT ON COLUMN products.specs IS 'Характеристики товара в формате JSONB'");
            
            // Комментарии к сгенерированным колонкам
            DB::statement("COMMENT ON COLUMN products.brand IS 'Бренд (автоматически извлекается из specs)'");
            DB::statement("COMMENT ON COLUMN products.color IS 'Цвет (автоматически извлекается из specs)'");
            DB::statement("COMMENT ON COLUMN products.country IS 'Страна (автоматически извлекается из specs)'");
            DB::statement("COMMENT ON COLUMN products.condition IS 'Состояние (автоматически извлекается из specs)'");
        }

        // Генерация UUID для существующих записей (если нужно)
        $this->generateUuids('users');
        $this->generateUuids('products');
    }

    private function generateUuids(string $table): void
    {
        DB::table($table)->whereNull('uuid')->get()->each(function ($item) use ($table) {
            DB::table($table)->where('id', $item->id)->update(['uuid' => str()->uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { $table->dropColumn('uuid'); });
        Schema::table('products', function (Blueprint $table) { $table->dropColumn('uuid'); });
    }
};
