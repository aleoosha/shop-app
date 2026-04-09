<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление комментариев к таблице содержимого корзины.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор строки в корзине')->change();
            
            $table->foreignId('cart_id')
                ->comment('ID корзины, к которой относится товар')
                ->change();

            $table->foreignId('product_id')
                ->comment('ID добавленного товара из каталога')
                ->change();

            $table->integer('quantity')
                ->comment('Количество единиц выбранного товара')
                ->change();

            $table->integer('price_at_addition')
                ->comment('Цена товара в копейках на момент его добавления в корзину (для отслеживания изменений)')
                ->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE cart_items IS 'Содержимое корзин: связь товаров с корзинами и их количество'");
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
