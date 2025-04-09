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
        Schema::create('curator_educations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curator_id');

            $table->string('university_en')->nullable();
            $table->string('university_ar')->nullable();
            $table->string('university_ku')->nullable();

            $table->string('college_en')->nullable();
            $table->string('college_ar')->nullable();
            $table->string('college_ku')->nullable();

            $table->string('education_major_en')->nullable();
            $table->string('education_major_ku')->nullable();
            $table->string('education_major_ar')->nullable();

            $table->string('scientific_department_en')->nullable();
            $table->string('scientific_department_ku')->nullable();
            $table->string('scientific_department_ar')->nullable();
            
            $table->foreign('curator_id')
                  ->references('id')
                  ->on('curators')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curator_educations');
    }
};
