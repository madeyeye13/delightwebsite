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
        Schema::table('orders', function (Blueprint $table) {
            // Gift card USED as payment at checkout (discount layer)
            $table->string('gift_card_code', 30)->nullable()->after('coupon_code');
            $table->unsignedInteger('gift_card_discount_amount')->default(0)->after('gift_card_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gift_card_code', 'gift_card_discount_amount']);
        });
    }
};
