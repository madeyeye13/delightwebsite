<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Contact snapshot (stored at order time)
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone', 30)->nullable();

            // Delivery address
            $table->string('shipping_country', 5)->default('NG');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_street')->nullable();
            $table->string('shipping_house_no', 50)->nullable();
            $table->string('shipping_postal', 20)->nullable();
            $table->text('shipping_notes')->nullable();

            // Shipping
            $table->string('shipping_method_id')->nullable(); // e.g. custom_lagos_island, dhl_P, store_pickup
            $table->string('shipping_carrier')->nullable();   // custom | dhl | store
            $table->string('shipping_method_name')->nullable();
            $table->unsignedInteger('shipping_cost')->default(0); // NGN
            $table->string('shipping_currency', 5)->default('NGN');
            $table->integer('shipping_estimated_days')->nullable();
            $table->boolean('shipping_contact_required')->default(false); // heavy item

            // Payment
            $table->string('payment_method')->nullable(); // paystack | flutterwave
            $table->string('payment_reference')->nullable();
            $table->string('payment_status')->default('pending'); // pending | paid | failed | refunded
            $table->timestamp('paid_at')->nullable();

            // Currency & totals (all in NGN stored as integer)
            $table->string('currency', 5)->default('NGN');
            $table->decimal('currency_rate', 12, 6)->default(1);
            $table->unsignedInteger('subtotal');          // items + add-ons in NGN
            $table->unsignedInteger('discount_amount')->default(0);
            $table->string('coupon_code', 50)->nullable();
            $table->unsignedInteger('total');             // subtotal - discount + shipping

            // Order status
            $table->string('status')->default('pending'); // pending | processing | shipped | delivered | cancelled
            $table->timestamp('reminder_sent_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('payment_status');
            $table->index('contact_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
