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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->enum('type', ['string', 'number', 'boolean']);
            $table->string('unit')->nullable();
            $table->boolean('is_filterable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
        });

        Schema::create('trim_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trim_id')->constrained('trims')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->string('value_string')->nullable();
            $table->decimal('value_number', 15, 4)->nullable();
            $table->boolean('value_boolean')->nullable();

            $table->unique(['trim_id', 'attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trim_attribute_values');
        Schema::dropIfExists('attributes');
    }
};
