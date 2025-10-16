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
        Schema::create('ethnicity_main_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Main ethnicity category code (A, B, C, D, E)');
            $table->string('name')->comment('Main ethnicity category name');
            $table->string('description')->nullable()->comment('Category description');
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
        Schema::dropIfExists('ethnicity_main_categories');
    }
};
