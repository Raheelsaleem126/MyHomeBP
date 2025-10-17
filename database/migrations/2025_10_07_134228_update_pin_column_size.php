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
        Schema::table('patients', function (Blueprint $table) {
            // Update PIN column to accommodate hashed values
            $table->string('pin', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear PIN data before reverting column size to avoid truncation errors
        DB::table('patients')->update(['pin' => null]);
        
        Schema::table('patients', function (Blueprint $table) {
            // Revert PIN column back to 4 characters
            $table->string('pin', 4)->change();
        });
    }
};