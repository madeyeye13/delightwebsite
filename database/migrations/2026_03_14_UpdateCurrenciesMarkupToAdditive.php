<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Changes markup from multiplier (1.0000) to additive amount.
     * Example: NGN = 0, USD = 4.50, GBP = 5.25 (additive markup to add to converted price)
     */
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            // Change markup to store additive amounts instead of multipliers
            $table->decimal('markup', 16, 2)->change()->default(0.00)->comment('Additive markup amount (not multiplier). e.g., 4.50 USD markup, 0 for NGN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->decimal('markup', 8, 4)->change()->default(1.0000)->comment('Multiplier on top of rate');
        });
    }
};
