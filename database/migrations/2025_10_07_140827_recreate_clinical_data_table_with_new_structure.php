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
        // Drop the existing clinical_data table
        Schema::dropIfExists('clinical_data');
        
        // Create new clinical_data table with correct structure
        Schema::create('clinical_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            
            // Height and Weight
            $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
            $table->decimal('bmi', 4, 1)->nullable()->comment('Calculated BMI');
            
            // Ethnicity (UK ONS codes)
            $table->string('ethnicity_code', 10)->nullable()->comment('UK ONS ethnicity code');
            $table->string('ethnicity_description')->nullable()->comment('Ethnicity description');
            
            // Smoking Status
            $table->enum('smoking_status', [
                'never_smoked',
                'current_smoker',
                'ex_smoker',
                'vaping',
                'occasional_smoker'
            ])->nullable();
            
            // Date of last blood test
            $table->date('last_blood_test_date')->nullable();
            
            // Urine Protein:Creatinine Ratio
            $table->decimal('urine_protein_creatinine_ratio', 8, 2)->nullable();
            
            // Comorbidities (multi-select)
            $table->json('comorbidities')->nullable()->comment('Selected comorbidities');
            $table->text('others_comorbidities')->nullable()->comment('Other comorbidities if "Others" selected');
            
            // Hypertension diagnosis
            $table->enum('hypertension_diagnosis', ['yes', 'no', 'dont_know'])->nullable();
            
            // Medications (BNF-linked)
            $table->json('medications')->nullable()->comment('BNF medications with dose and frequency');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('clinical_data');
        
        // Recreate the old table structure (if needed for rollback)
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
            $table->json('medications')->nullable();
            $table->json('comorbidities')->nullable();
            $table->date('last_blood_test')->nullable();
            $table->decimal('urine_protein_creatinine_ratio', 8, 2)->nullable();
            $table->timestamps();
        });
    }
};