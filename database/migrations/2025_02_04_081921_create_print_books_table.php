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
        Schema::create('print_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id'); 

            $table->enum('year_type',['hijri','AD'])->nullable();
            $table->string('publisher_ar')->nullable();
            $table->string('publisher_en')->nullable();
            $table->string('publisher_ku')->nullable();

            $table->string('print_house_ar')->nullable();
            $table->string('print_house_en')->nullable();
            $table->string('print_house_ku')->nullable();

            $table->string('print_location_ar')->nullable();
            $table->string('print_location_en')->nullable();
            $table->string('print_location_ku')->nullable();

            $table->year('print_year')->nullable();
            // $table->string('print_properties')->nullable();



            $table->foreign('book_id')
              ->references('id')->on('books')
              ->onDelete('cascade');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_books');
    }
};
