<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Обновляем users
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->unique()->nullable();
        });

        // 2. Добавляем комментарии через прямой SQL (безопасно для Postgres)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE users IS 'Таблица учетных записей пользователей'");
        }

        // Генерация UUID для существующих записей
        $this->generateUuids('users');
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
    }
};
