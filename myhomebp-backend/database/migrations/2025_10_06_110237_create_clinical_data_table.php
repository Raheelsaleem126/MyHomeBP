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
        Schema::create('clinical_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->integer('height_cm')->nullable();
            $table->integer('height_ft')->nullable();
            $table->integer('height_inches')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->integer('weight_stones')->nullable();
            $table->decimal('weight_lbs', 5, 2)->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->string('ethnicity')->nullable();
            $table->enum('smoking_status', [
                'never_smoked',
                'current_smoker', 
                'ex_smoker',
                'vaping',
                'occasional_smoker'
            ])->nullable();
            $table->boolean('hypertension_diagnosis')->nullable();
            $table->json('medications')->nullable(); // Store BNF medications as JSON
            $table->json('comorbidities')->nullable(); // Store comorbidities as JSON array
            $table->date('last_blood_test')->nullable();
            $table->decimal('urine_protein_creatinine_ratio', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_data');
    }
};