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
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('users', 'referred_by_staff_id')) {
                $table->unsignedInteger('referred_by_staff_id')->nullable()->after('referral_code');
            }
            if (!Schema::hasColumn('users', 'referred_by_code')) {
                $table->string('referred_by_code')->nullable()->after('referred_by_staff_id');
            }
            
            // Skip foreign key constraint for now to avoid issues
            // Will add it manually later if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referred_by_staff_id', 'referred_by_code']);
        });
    }
};