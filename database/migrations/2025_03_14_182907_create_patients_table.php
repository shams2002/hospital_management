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
            $table->string('social_status');
            $table->string('emergency_num');
            $table->string('insurance_company');
            $table->string('insurance_num');
            $table->boolean('smoker')->default(false);
            $table->boolean('pregnant')->default(false);
            $table->string('blood_type');
            $table->string('genetic_diseases');
            $table->string('chronic_diseases');
            $table->string('drug_allergy');
            $table->string('last_operations');
            $table->string('present_medicines');
            $table->string('status');
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
