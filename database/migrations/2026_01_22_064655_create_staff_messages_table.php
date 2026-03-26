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
    $table->foreignId('staff_id')->nullable()->constrained('staff')->cascadeOnDelete();            $table->string('recipient_type')->default('specific'); // 'specific', 'all', 'by_role'
                $table->string('subject');
                $table->text('message');
                $table->timestamp('read_at')->nullable(); // To track if message has been read
                $table->timestamps();
                
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
