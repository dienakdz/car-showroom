<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            });

            // Mark existing users as verified so they can log in without issue
            DB::table('users')->whereNull('email_verified_at')->update([
                'email_verified_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('email_verified_at');
            });
        }
    }
};
