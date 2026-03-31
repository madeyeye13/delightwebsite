<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tracks every time a referral code is used at checkout
        Schema::create('referral_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            // The user who used the referral (null if they checked out as guest without registering)
            $table->foreignId('used_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('discount_amount');  // NGN discount applied
            $table->unsignedInteger('points_awarded');   // points given to referrer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_uses');
    }
};
