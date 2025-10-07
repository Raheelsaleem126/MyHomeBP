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
        Schema::create('ethnicity_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('UK ONS ethnicity code');
            $table->string('description')->comment('Ethnicity description');
            $table->string('category')->nullable()->comment('Ethnicity category');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethnicity_codes');
    }
};