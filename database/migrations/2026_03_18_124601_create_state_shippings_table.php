<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('state_shippings', function (Blueprint $table) {
            $table->id();
            $table->string('state_name', 60)->unique();
            $table->string('state_code', 5)->unique();
            $table->unsignedInteger('shipping_fee');  // NGN base fee
            $table->string('currency', 5)->default('NGN');
            $table->unsignedSmallInteger('estimated_days')->default(3);
            $table->boolean('is_active')->default(true);

            // Weight tiers — all configurable from admin
            $table->decimal('tier_1_limit', 5, 2)->default(3.00);  // kg: <= this = normal fee
            $table->decimal('tier_2_limit', 5, 2)->default(5.00);  // kg: <= this = fee + tier_2_surcharge
            $table->unsignedInteger('tier_2_surcharge')->default(1500); // NGN
            $table->decimal('tier_3_limit', 5, 2)->default(8.00);  // kg: <= this = fee + tier_3_surcharge
            $table->unsignedInteger('tier_3_surcharge')->default(3000); // NGN
            $table->boolean('contact_for_heavy')->default(true); // >tier_3_limit = contact admin

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('state_shippings');
    }
};
