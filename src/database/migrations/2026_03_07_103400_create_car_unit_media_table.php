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
        Schema::create('car_unit_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_unit_id')->constrained('car_units')->cascadeOnDelete();
            $table->enum('type', ['image', 'video']);
            $table->text('path_or_url');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_unit_media');
    }
};
