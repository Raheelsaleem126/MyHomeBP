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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('bnf_code', 20)->unique()->comment('BNF code');
            $table->string('generic_name')->comment('Generic medication name');
            $table->string('brand_name')->nullable()->comment('Brand name');
            $table->string('form')->nullable()->comment('Form (tablet, capsule, etc.)');
            $table->string('strength')->nullable()->comment('Strength (mg, etc.)');
            $table->text('description')->nullable()->comment('Full description');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for search
            $table->index(['generic_name', 'brand_name']);
            $table->index('bnf_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};