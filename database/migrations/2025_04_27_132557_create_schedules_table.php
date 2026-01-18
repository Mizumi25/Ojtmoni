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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('agency_id')->constrained()->onDelete('cascade'); // Foreign key to agencies table
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']); // Day of the week
            $table->time('expected_morning_in'); // Expected morning check-in time
            $table->time('expected_morning_out'); // Expected morning check-out time
            $table->time('expected_afternoon_in'); // Expected afternoon check-in time
            $table->time('expected_afternoon_out'); // Expected afternoon check-out time
            $table->integer('late_tolerance')->default(15);
            $table->integer('grace_period')->default(15);// Allow 15 minutes of tolerance for late check-ins (in minutes)
            $table->time('overtime_allowed')->nullable(); // Overtime allowed duration (NULL if no overtime is allowed)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
