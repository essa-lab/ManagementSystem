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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_scientific_classification_id');
            $table->unsignedBigInteger('article_specification_id');
            $table->unsignedBigInteger('article_type_id');

            $table->string('registeration_number');
            $table->string('order_number')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->string('subtitle_ku')->nullable();
            $table->string('secondary_title_en')->nullable();
            $table->string('secondary_title_ar')->nullable();
            $table->string('secondary_title_ku')->nullable();
            $table->date('publication_date')->nullable();
            $table->year('research_year')->nullable();
            $table->string('duration_of_research')->nullable();
            $table->string('place_of_printing_en')->nullable();
            $table->string('place_of_printing_ar')->nullable();
            $table->string('place_of_printing_ku')->nullable();

            $table->string('course')->nullable();
            $table->string('number')->nullable();
            $table->integer('number_of_pages')->nullable();
            $table->boolean('map')->default(0);
            $table->string('journal_name')->nullable();
            $table->string('journal_volume')->nullable();

            $table->foreign('article_scientific_classification_id')
                  ->references('id')
                  ->on('scientific_branches')->onDelete('cascade');
            $table->foreign('article_specification_id')
                  ->references('id')
                  ->on('specifications')->onDelete('cascade');
            $table->foreign('article_type_id')
                  ->references('id')
                  ->on('article_types')->onDelete('cascade');


            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
