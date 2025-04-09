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
        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('education_level_id');
            $table->unsignedBigInteger('research_type_id');
            $table->unsignedBigInteger('research_format_id');

            $table->string('registeration_number');
            $table->string('order_number')->nullable();
            $table->date('publish_date')->nullable();
            $table->date('discussion_date')->nullable();
            $table->integer('number_of_pages')->nullable();
            $table->string('price')->nullable();
            $table->string('classification')->nullable();
            $table->string('status')->nullable();
            $table->string('university_en')->nullable();
            $table->string('university_ar')->nullable();
            $table->string('university_ku')->nullable();

            $table->string('college_en')->nullable();
            $table->string('college_ar')->nullable();
            $table->string('college_ku')->nullable();

            $table->string('education_major_en')->nullable();
            $table->string('education_major_ku')->nullable();
            $table->string('education_major_ar')->nullable();


            $table->foreign('education_level_id')
                  ->references('id')
                  ->on('education_levels')->onDelete('cascade');
            $table->foreign('research_type_id')
                  ->references('id')
                  ->on('research_types')->onDelete('cascade');
            $table->foreign('research_format_id')
                  ->references('id')
                  ->on('research_formats')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
