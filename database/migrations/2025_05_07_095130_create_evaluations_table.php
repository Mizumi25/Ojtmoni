<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('evaluator_name')->nullable();

            // SCORES (1-5)
            $table->integer('demonstrates_professionalism')->nullable();
            $table->integer('communicates_effectively')->nullable();
            $table->integer('shows_initiative_and_creativity')->nullable();
            $table->integer('works_well_with_others')->nullable();
            $table->integer('completes_tasks_on_time')->nullable();
            $table->integer('follows_company_policies')->nullable();
            $table->integer('adapts_to_work_environment')->nullable();

            // SCORES
            $table->integer('technical_skills_score')->nullable();
            $table->integer('attendance_score')->nullable();
            $table->integer('overall_performance_score')->nullable();

            // COMMENTS
            $table->text('evaluator_comments')->nullable();

            // SIGNATURE
            $table->longText('signature')->nullable(); // Add this line

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};