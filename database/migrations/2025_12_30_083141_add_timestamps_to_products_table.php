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
        // Rename existing camelCase timestamps to Laravel's snake_case convention
        if (Schema::hasColumn('products', 'createdAt') && !Schema::hasColumn('products', 'created_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('createdAt', 'created_at');
            });
        }
        
        if (Schema::hasColumn('products', 'updatedAt') && !Schema::hasColumn('products', 'updated_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('updatedAt', 'updated_at');
            });
        }
        
        // If timestamps don't exist at all, add them
        if (!Schema::hasColumn('products', 'created_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }
        
        if (!Schema::hasColumn('products', 'updated_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename timestamps back to camelCase if needed
        if (Schema::hasColumn('products', 'created_at') && !Schema::hasColumn('products', 'createdAt')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('created_at', 'createdAt');
            });
        }
        
        if (Schema::hasColumn('products', 'updated_at') && !Schema::hasColumn('products', 'updatedAt')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('updated_at', 'updatedAt');
            });
        }
    }
};
