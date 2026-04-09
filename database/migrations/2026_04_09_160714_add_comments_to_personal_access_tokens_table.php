<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к системной таблице токенов Sanctum.
     */
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор токена')->change();
            
            // Поля полиморфной связи (tokenable_type и tokenable_id)
            $table->string('tokenable_type')->comment('Класс модели владельца (обычно App\Models\User)')->change();
            $table->unsignedBigInteger('tokenable_id')->comment('ID владельца токена')->change();
            
            $table->text('name')->comment('Человекочитаемое имя токена (например, "iPhone 15")')->change();
            $table->string('token', 64)->comment('Хешированное значение API-токена')->change();
            $table->text('abilities')->nullable()->comment('Список разрешенных действий (scopes) в формате JSON')->change();
            $table->timestamp('last_used_at')->nullable()->comment('Время последней активности по данному токену')->change();
            $table->timestamp('expires_at')->nullable()->comment('Время истечения срока действия токена')->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE personal_access_tokens IS 'Реестр выданных API-токенов доступа (Sanctum)'");
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
