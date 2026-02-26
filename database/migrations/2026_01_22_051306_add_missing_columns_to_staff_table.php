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
        Schema::table('staff', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('bank_account_number')->nullable()->after('password');
            $table->string('ifsc_code')->nullable()->after('bank_account_number');
            $table->string('bank_name')->nullable()->after('ifsc_code');
            $table->string('address')->nullable()->after('bank_name');
            $table->string('village')->nullable()->after('address');
            $table->string('district')->nullable()->after('village');
            $table->string('pincode')->nullable()->after('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'bank_account_number',
                'ifsc_code',
                'bank_name',
                'address',
                'village',
                'district',
                'pincode'
            ]);
        });
    }
};
