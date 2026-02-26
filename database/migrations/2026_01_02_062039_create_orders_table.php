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
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Removed constraint for compatibility
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            
            // Money
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Customer Details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            
            // Address
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            
            // Payment
            $table->string('payment_method'); // cod, online
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            
            // Meta
            $table->string('referral_code')->nullable();
            
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
