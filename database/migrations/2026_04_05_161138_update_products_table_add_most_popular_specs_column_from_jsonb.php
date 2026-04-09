<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->storedAs("specs->>'brand'")->nullable();
            $table->string('country')->storedAs("specs->>'country'")->nullable();
            $table->string('color')->storedAs("specs->>'color'")->nullable();
            $table->string('condition')->storedAs("specs->>'condition'")->nullable();

            $table->index('brand');
            $table->index('color');
            $table->index('country');
            $table->index('condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['brand']);
            $table->dropIndex(['color']);
            $table->dropIndex(['country']);
            $table->dropIndex(['condition']);

            $table->dropColumn(['brand', 'country', 'color', 'condition']);
        });
    }
};
