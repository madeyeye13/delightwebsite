<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dhl_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('dhl_tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('shipment_id')->nullable();
            $table->string('product_code', 10)->default('P');
            $table->string('product_name')->nullable();
            $table->decimal('base_rate', 10, 2)->nullable();
            $table->string('base_currency', 3)->nullable();
            $table->decimal('markup_percentage', 5, 2)->nullable();
            $table->decimal('markup_amount', 10, 2)->nullable();
            $table->decimal('final_rate', 10, 2)->nullable();
            $table->string('billing_currency', 3)->nullable();
            $table->timestamp('estimated_delivery_date')->nullable();
            $table->integer('total_transit_days')->nullable();
            $table->decimal('total_weight', 8, 3)->nullable();
            $table->string('weight_unit', 10)->default('kg');
            $table->json('rate_response')->nullable();
            $table->json('shipment_response')->nullable();
            $table->string('status', 20)->default('pending'); // pending | created | in_transit | delivered | cancelled
            $table->longText('label_data')->nullable();       // base64-encoded PDF label
            $table->string('label_format', 10)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dhl_shipments');
    }
};
