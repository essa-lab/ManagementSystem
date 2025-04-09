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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_id');
            $table->unsignedBigInteger('language_id')->nullable();

            $table->string('title_en');
            $table->string('title_ar');
            $table->string('title_ku');
            $table->string('uuid');
            $table->string('link');
            $table->integer('number_of_copies')->nullable();
            $table->date('registry_date')->nullable();
            $table->date('arrival_date')->nullable();

            $table->morphs('resourceable');

            $table->foreign('library_id')
            ->references('id')->on('libraries')
            ->onDelete('cascade'); 
            $table->foreign('language_id')
            ->references('id')->on('languages')
            ->onDelete('set null');
             
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource');
    }
};
