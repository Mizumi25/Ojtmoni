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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete(); // null if group
            $table->foreignId('group_id')->nullable()->constrained('message_groups')->nullOnDelete(); 
            $table->text('content')->nullable();
            $table->string('media_path')->nullable(); // image/video path
            $table->enum('type', ['text', 'image', 'video', 'announcement', 'agency'])->default('text');
            $table->foreignId('shared_announcement_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shared_agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
