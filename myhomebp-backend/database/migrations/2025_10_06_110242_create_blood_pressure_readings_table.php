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
        Schema::create('blood_pressure_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->dateTime('reading_date');
            $table->enum('session_type', ['am', 'pm']);
            $table->integer('reading_1_systolic');
            $table->integer('reading_1_diastolic');
            $table->integer('reading_1_pulse');
            $table->integer('reading_2_systolic');
            $table->integer('reading_2_diastolic');
            $table->integer('reading_2_pulse');
            $table->integer('reading_3_systolic')->nullable();
            $table->integer('reading_3_diastolic')->nullable();
            $table->integer('reading_3_pulse')->nullable();
            $table->boolean('is_high_reading')->default(false);
            $table->boolean('requires_urgent_advice')->default(false);
            $table->text('system_response')->nullable();
            $table->timestamps();
            
            // Index for efficient querying
            $table->index(['patient_id', 'reading_date']);
            $table->index(['patient_id', 'session_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_pressure_readings');
    }
};