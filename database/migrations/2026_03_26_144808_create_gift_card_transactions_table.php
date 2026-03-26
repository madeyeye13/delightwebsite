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
        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gift_card_id')->constrained('gift_cards')->cascadeOnDelete();

            // How much was used in this transaction
            $table->unsignedBigInteger('amount_used');
            $table->unsignedBigInteger('balance_before');
            $table->unsignedBigInteger('balance_after');

            // Online order that triggered this redemption (nullable for POS)
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            // Admin/staff who processed this (for POS and admin-initiated)
            $table->foreignId('redeemed_by_admin_id')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('is_pos_redemption')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('gift_card_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_card_transactions');
    }
};
