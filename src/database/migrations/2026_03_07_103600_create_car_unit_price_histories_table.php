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
        Schema::create('car_unit_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_unit_id')->constrained('car_units')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('old_price')->nullable();
            $table->unsignedBigInteger('new_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_unit_price_histories');
    }
};
