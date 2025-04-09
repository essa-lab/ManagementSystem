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
        Schema::create('about_contact', function (Blueprint $table) {
            $table->id();

            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_ku')->nullable();

            $table->string('description_ku')->nullable();
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();

            $table->string('location_title_en')->nullable();
            $table->string('location_title_ar')->nullable();
            $table->string('location_title_ku')->nullable();

            
            $table->string('location_description_en')->nullable();
            $table->string('location_description_ar')->nullable();
            $table->string('location_description_ku')->nullable();

            $table->string('coordinates')->nullable();




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_contact');
    }
};
