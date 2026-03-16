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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            // rate = how many of this currency per 1 NGN (NGN is always base)
            // e.g. USD rate = 0.00065 means 1 NGN = 0.00065 USD
            $table->decimal('rate', 16, 8)->default(1.00000000);
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
