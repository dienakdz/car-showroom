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
        Schema::create('car_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trim_id')->constrained('trims')->cascadeOnDelete();
            $table->enum('condition', ['new', 'used', 'cpo']);
            $table->string('vin')->nullable()->unique();
            $table->string('stock_code')->unique();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('mileage')->nullable();
            $table->foreignId('body_type_id')->nullable()->constrained('body_types')->nullOnDelete();
            $table->foreignId('fuel_type_id')->nullable()->constrained('fuel_types')->nullOnDelete();
            $table->foreignId('transmission_id')->nullable()->constrained('transmissions')->nullOnDelete();
            $table->foreignId('drivetrain_id')->nullable()->constrained('drivetrains')->nullOnDelete();
            $table->foreignId('exterior_color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('interior_color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'available', 'on_hold', 'sold', 'archived'])->default('draft');
            $table->timestamp('hold_until')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->text('notes_internal')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_units');
    }
};
