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
        if (! Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->rememberToken();
            });
        }

        Schema::table('car_units', function (Blueprint $table): void {
            $table->index(['status', 'published_at', 'id'], 'car_units_status_published_id_idx');
            $table->index(['status', 'condition', 'published_at', 'id'], 'car_units_status_condition_published_id_idx');
            $table->index(['status', 'year'], 'car_units_status_year_idx');
            $table->index(['status', 'price'], 'car_units_status_price_idx');
            $table->index(['status', 'mileage'], 'car_units_status_mileage_idx');
        });

        Schema::table('car_unit_media', function (Blueprint $table): void {
            $table->index(['car_unit_id', 'is_cover', 'sort_order'], 'car_unit_media_cover_lookup_idx');
            $table->index(['car_unit_id', 'type', 'is_cover', 'sort_order'], 'car_unit_media_gallery_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_unit_media', function (Blueprint $table): void {
            $table->dropIndex('car_unit_media_gallery_lookup_idx');
            $table->dropIndex('car_unit_media_cover_lookup_idx');
        });

        Schema::table('car_units', function (Blueprint $table): void {
            $table->dropIndex('car_units_status_mileage_idx');
            $table->dropIndex('car_units_status_price_idx');
            $table->dropIndex('car_units_status_year_idx');
            $table->dropIndex('car_units_status_condition_published_id_idx');
            $table->dropIndex('car_units_status_published_id_idx');
        });

        if (Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropRememberToken();
            });
        }
    }
};
