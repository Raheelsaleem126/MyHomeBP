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
        Schema::create('comorbidities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Comorbidity code (e.g., stroke, diabetes_type_1)');
            $table->string('name')->comment('Comorbidity name');
            $table->text('description')->nullable()->comment('Detailed description');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0)->comment('Order for display');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comorbidities');
    }
};
