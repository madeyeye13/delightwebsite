<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label');        // Human-readable for admin UI
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed defaults — all configurable from admin
        DB::table('reward_settings')->insert([
            [
                'key' => 'points_per_referral',
                'value' => '100',
                'label' => 'Points per referral',
                'description' => 'Points awarded to the referrer when someone uses their code.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'naira_per_point',
                'value' => '10',
                'label' => 'Naira value per point',
                'description' => 'How many Naira each reward point is worth at redemption.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_points_per_order',
                'value' => '300',
                'label' => 'Max points redeemable per order',
                'description' => 'Maximum reward points a user can spend on a single order.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'referral_discount_percent',
                'value' => '5',
                'label' => 'Referral discount (%)',
                'description' => 'Percentage discount applied to the referred customer\'s first order.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_settings');
    }
};
