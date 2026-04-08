<?php

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
            $table->jsonb('specs')->default('{}');
        });
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX products_specs_gin ON products USING GIN (specs jsonb_path_ops)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('specs');
        });

        DB::statement('DROP INDEX IF EXISTS products_specs_gin');
    }
};
