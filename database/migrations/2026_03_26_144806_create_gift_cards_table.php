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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();

            // The unique redeemable code — e.g. DLT-A3KP-9ZMX-7WQR
            $table->string('code', 30)->unique();

            $table->enum('status', ['active', 'redeemed', 'expired', 'cancelled'])->default('active');

            // Balance in NGN (integer kobo-style, whole naira)
            $table->unsignedBigInteger('initial_balance');
            $table->unsignedBigInteger('current_balance');

            // Who purchased this gift card (via online order)
            $table->foreignId('purchased_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('purchased_order_id')->nullable()->constrained('orders')->nullOnDelete();

            // Optional recipient info (for sending to someone else)
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('personal_message')->nullable();

            // POS-issued (created directly by admin without an order)
            $table->boolean('is_pos_issued')->default(false);
            $table->foreignId('issued_by_admin_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('status');
            $table->index('recipient_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
