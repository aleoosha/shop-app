<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблица заголовков заказов
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Внутренний инкрементный идентификатор');
            $table->uuid('uuid')->unique()->comment('Публичный уникальный идентификатор для API и URL');
            
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Привязка к пользователю, совершившему заказ');

            $table->integer('total_price')
                ->comment('Итоговая сумма заказа в минимальных денежных единицах (копейках)');

            $table->string('status')
                ->default(OrderStatus::PENDING->value)
                ->comment('Текущий статус жизненного цикла заказа (pending, processing, etc.)');

            $table->timestamps();
            
            $table->comment('Таблица основных данных заказа');
        });

        // Таблица состава заказа (Snapshots)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('Внутренний идентификатор строки состава');
            
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Связь с основным заказом');

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('Связь с товаром (может быть NULL, если товар удален из каталога)');

            $table->integer('price_at_purchase')
                ->comment('Фиксированная цена товара в копейках на момент оформления заказа');

            $table->string('title_at_purchase')
                ->comment('Снапшот названия товара на момент оформления заказа');

            $table->integer('quantity')
                ->default(1)
                ->comment('Количество единиц товара в данной строке');

            $table->timestamps();

            $table->comment('Состав заказа: фиксирует состояние товаров на момент покупки');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
