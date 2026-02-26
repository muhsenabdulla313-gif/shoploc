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
        Schema::create('staff_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id')->nullable(); // Match staff table's int(11) id type
            $table->string('recipient_type')->default('specific'); // 'specific', 'all', 'by_role'
            $table->string('subject');
            $table->text('message');
            $table->timestamp('read_at')->nullable(); // To track if message has been read
            $table->timestamps();
            
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_messages');
    }
};
