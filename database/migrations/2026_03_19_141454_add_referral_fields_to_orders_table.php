<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // The referral code applied at checkout (separate from coupon_code)
            $table->string('referral_code', 12)->nullable()->after('coupon_code');
            $table->unsignedInteger('referral_discount_amount')->default(0)->after('referral_code');
            // Points redeemed from wallet at checkout
            $table->unsignedInteger('points_redeemed')->default(0)->after('referral_discount_amount');
            $table->unsignedInteger('points_discount_amount')->default(0)->after('points_redeemed');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'referral_discount_amount', 'points_redeemed', 'points_discount_amount']);
        });
    }
};