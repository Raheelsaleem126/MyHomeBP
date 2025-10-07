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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('surname');
            $table->date('date_of_birth');
            $table->text('address');
            $table->string('mobile_phone', 20);
            $table->string('home_phone', 20)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('data_sharing_consent')->default(false);
            $table->boolean('notifications_consent')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};