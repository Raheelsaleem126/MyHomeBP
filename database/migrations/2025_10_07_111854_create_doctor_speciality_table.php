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
        Schema::create('doctor_speciality', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('speciality_id')->constrained()->onDelete('cascade');
            $table->date('certification_date')->nullable();
            $table->string('certification_body')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_primary')->default(false); // Primary specialty
            $table->timestamps();

            // Ensure unique combination of doctor and speciality
            $table->unique(['doctor_id', 'speciality_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_speciality');
    }
};