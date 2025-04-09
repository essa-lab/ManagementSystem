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
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->string('registeration_number');
            $table->string('order_number')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->string('subtitle_ku')->nullable();
            $table->string('translated_title_en')->nullable();
            $table->string('translated_title_ar')->nullable();
            $table->string('translated_title_ku')->nullable();
            $table->string('location_of_congress')->nullable();
            $table->string('dewey_decimal_classification')->nullable();
            $table->string('volume_number')->nullable();
            $table->string('volume')->nullable();
            $table->string('print_circulation')->nullable();
            $table->string('department')->nullable();
            $table->string('table_of_content_condition')->nullable();
            $table->string('cover_specification')->nullable();
            $table->string('book_national_id_number')->nullable();
            $table->string('editor')->nullable();
            $table->string('publishing_house_ar')->nullable();
            $table->string('publishing_house_ku')->nullable();
            $table->string('publishing_house_en')->nullable();
            $table->string('price')->nullable();
            $table->string('isbn')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('number_of_pages')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
