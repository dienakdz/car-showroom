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
        Schema::create('feature_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
        });

        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_group_id')->constrained('feature_groups')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
        });

        Schema::create('trim_feature', function (Blueprint $table) {
            $table->foreignId('trim_id')->constrained('trims')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->primary(['trim_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trim_feature');
        Schema::dropIfExists('features');
        Schema::dropIfExists('feature_groups');
    }
};
