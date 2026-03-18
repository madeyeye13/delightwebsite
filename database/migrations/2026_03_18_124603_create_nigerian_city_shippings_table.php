<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nigerian_city_shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_shipping_id')->constrained('state_shippings')->cascadeOnDelete();
            $table->string('city_name', 80);
            $table->unsignedInteger('shipping_fee');  // NGN base fee
            $table->string('currency', 5)->default('NGN');
            $table->unsignedSmallInteger('estimated_days')->default(2);
            $table->boolean('is_active')->default(true);

            // Null = inherit from parent state_shipping
            $table->decimal('tier_1_limit', 5, 2)->nullable();
            $table->decimal('tier_2_limit', 5, 2)->nullable();
            $table->unsignedInteger('tier_2_surcharge')->nullable();
            $table->decimal('tier_3_limit', 5, 2)->nullable();
            $table->unsignedInteger('tier_3_surcharge')->nullable();
            $table->boolean('contact_for_heavy')->nullable();

            $table->timestamps();

            $table->unique(['state_shipping_id', 'city_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nigerian_city_shippings');
    }
};
