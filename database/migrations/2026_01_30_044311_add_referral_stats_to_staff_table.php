<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedInteger('purchases_referred')->default(0)->after('score');
            $table->decimal('total_earnings', 10, 2)->default(0)->after('purchases_referred');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['purchases_referred', 'total_earnings']);
        });
    }
};
