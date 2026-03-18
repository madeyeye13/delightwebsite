<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();

            // Snapshot at time of order
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->string('selling_method')->nullable(); // per_length, per_piece, etc.
            $table->string('unit_label')->nullable();
            $table->unsignedSmallInteger('units_per_order')->default(1);

            $table->unsignedInteger('unit_price');  // NGN
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('total_price'); // NGN
            $table->decimal('weight_kg', 8, 3)->nullable();

            $table->boolean('is_addon')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
