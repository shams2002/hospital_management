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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('father_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('birth_date');
            $table->string('national_number');
            $table->text('address');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('license_number')->nullable();
            $table->string('experience_years')->nullable();
            $table->string('meet_cost');
            $table->string('image')->nullable();
            $table->text('bio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
