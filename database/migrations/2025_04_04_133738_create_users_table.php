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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('profile_picture')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('student');
            
            $table->string('phone_number')->nullable(); 

            $table->string('student_id')->nullable()->unique();
            $table->foreignId('course_id')->nullable()->constrained('courses'); 
            $table->foreignId('year_level_id')->nullable()->constrained('year_levels'); 
            $table->foreignId('course_offering_id')->nullable()->constrained('course_offerings')->onDelete('set null');
            $table->string('school_id_image')->nullable();
            
            $table->string('status')->nullable()->default(null);
            $table->boolean('map_exposed')->default(false);
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            
            $table->decimal('remaining_hours', 6, 2)->default(300.00);
            
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            
            $table->softDeletes();

            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
