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
        Schema::create('product_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('code');
            $table->unsignedSmallInteger('discount_percent');
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('max_uses')->default(0);   // 0 = unlimited
            $table->unsignedInteger('uses_count')->default(0);
            $table->unsignedBigInteger('min_order_amount')->default(0);
            $table->boolean('new_users_only')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_coupons');
    }
};
