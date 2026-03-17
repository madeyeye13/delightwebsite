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
            $table->decimal('weight', 8, 3)->nullable()->after('cost');
            $table->string('weight_unit', 10)->default('kg')->after('weight');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('weight', 8, 3)->nullable()->after('price_adjustment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'weight_unit']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
