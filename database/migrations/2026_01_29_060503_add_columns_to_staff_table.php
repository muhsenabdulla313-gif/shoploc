<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('ref_code')->unique()->nullable(); // Unique referral code
            $table->integer('score')->default(0); // Score for referrals
            $table->boolean('is_active')->default(true); // Active status
        });
        
        // Update existing staff records to have ref_code if they don't already have referral_code
        DB::statement("UPDATE staff SET ref_code = referral_code WHERE ref_code IS NULL AND referral_code IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['ref_code', 'score', 'is_active']);
        });
    }
};