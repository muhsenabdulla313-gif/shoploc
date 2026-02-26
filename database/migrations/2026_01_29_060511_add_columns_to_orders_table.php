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
        // Add columns to orders table only if they don't exist
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable(); // Foreign key to staff
            }
            if (!Schema::hasColumn('orders', 'ref_code')) {
                $table->string('ref_code')->nullable(); // Referral code used
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'staff_id')) {
                $table->dropColumn('staff_id');
            }
            if (Schema::hasColumn('orders', 'ref_code')) {
                $table->dropColumn('ref_code');
            }
        });
    }
};