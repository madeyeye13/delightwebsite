<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dhl_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key', 60)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string | boolean | integer | float
            $table->string('label', 100)->nullable();       // human-readable label for admin UI
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dhl_configurations');
    }
};
