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
        Schema::table('blood_pressure_readings', function (Blueprint $table) {
            $table->integer('average_systolic')->nullable();
            $table->integer('average_diastolic')->nullable();
            $table->integer('average_pulse')->nullable();
            $table->string('reading_category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_pressure_readings', function (Blueprint $table) {
            $table->dropColumn(['average_systolic', 'average_diastolic', 'average_pulse', 'reading_category']);
        });
    }
};
