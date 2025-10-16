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
        Schema::create('ethnicity_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_category_id')->constrained('ethnicity_main_categories')->onDelete('cascade');
            $table->string('code', 10)->comment('Subcategory code (A1-A5, B1-B4, etc.)');
            $table->string('name')->comment('Subcategory name');
            $table->string('description')->nullable()->comment('Subcategory description');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0)->comment('Order for display within main category');
            $table->timestamps();
            
            $table->unique(['main_category_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethnicity_subcategories');
    }
};
