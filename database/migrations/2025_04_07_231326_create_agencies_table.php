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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('agency_name');
            $table->text('agency_background')->nullable();
            $table->foreignId('contact_person_id')->constrained('users')->onDelete('cascade'); 
            $table->string('agency_number')->nullable();
            $table->integer('slot')->default(0);
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->decimal('agency_radius', 8, 2)->nullable();
            $table->string('agency_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
