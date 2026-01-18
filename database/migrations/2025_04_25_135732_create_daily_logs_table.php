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
        
            Schema::create('daily_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->time('morning_in')->nullable();
                $table->time('morning_out')->nullable();
                $table->time('afternoon_in')->nullable();
                $table->time('afternoon_out')->nullable();
                $table->decimal('hours_rendered', 5, 2)->default(0.00);
                $table->enum('status', ['pending', 'completed', 'missed', 'late'])->default('pending'); // Enum for status
                $table->text('signature')->nullable(); // store Base64 image string
                $table->timestamps();
        
                $table->unique(['user_id', 'date']); // only one record per student per day
            });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
