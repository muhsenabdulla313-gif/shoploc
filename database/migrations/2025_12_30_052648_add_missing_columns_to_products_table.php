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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0);
            }
            if (!Schema::hasColumn('products', 'gallery_images')) {
                $table->json('gallery_images')->nullable();
            }
            if (!Schema::hasColumn('products', 'sizes')) {
                $table->json('sizes')->nullable();
            }
            if (!Schema::hasColumn('products', 'colors')) {
                $table->json('colors')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'original_price')) {
                $table->dropColumn('original_price');
            }
            if (Schema::hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }
            if (Schema::hasColumn('products', 'gallery_images')) {
                $table->dropColumn('gallery_images');
            }
            if (Schema::hasColumn('products', 'sizes')) {
                $table->dropColumn('sizes');
            }
            if (Schema::hasColumn('products', 'colors')) {
                $table->dropColumn('colors');
            }
        });
    }
};
