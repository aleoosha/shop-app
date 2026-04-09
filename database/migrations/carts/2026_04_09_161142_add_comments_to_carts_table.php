<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к таблице корзин.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор корзины')->change();
            
            $table->foreignId('user_id')
                ->nullable()
                ->comment('ID владельца корзины (заполняется для авторизованных пользователей)')
                ->change();

            $table->uuid('guest_id')
                ->nullable()
                ->comment('Уникальный UUID для идентификации корзины гостя (хранится в куках)')
                ->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE carts IS 'Таблица активных корзин покупателей'");
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
