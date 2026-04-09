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
        if (DB::getDriverName() === 'pgsql') {
            // Таблица CARTS
            DB::statement("COMMENT ON COLUMN carts.id IS 'Внутренний идентификатор корзины'");
            DB::statement("COMMENT ON COLUMN carts.user_id IS 'ID владельца корзины (для авторизованных)'");
            DB::statement("COMMENT ON COLUMN carts.guest_id IS 'UUID для идентификации корзины гостя'");

            // Таблица CART_ITEMS
            DB::statement("COMMENT ON COLUMN cart_items.id IS 'ID строки товара в корзине'");
            DB::statement("COMMENT ON COLUMN cart_items.cart_id IS 'Связь с основной корзиной'");
            DB::statement("COMMENT ON COLUMN cart_items.product_id IS 'Связь с товаром из каталога'");
            DB::statement("COMMENT ON COLUMN cart_items.quantity IS 'Количество единиц товара'");
            DB::statement("COMMENT ON COLUMN cart_items.price_at_addition IS 'Цена товара в копейках на момент добавления'");

            // Комментарии к самим таблицам
            DB::statement("COMMENT ON TABLE carts IS 'Активные корзины покупателей'");
            DB::statement("COMMENT ON TABLE cart_items IS 'Состав корзин покупателей'");
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
