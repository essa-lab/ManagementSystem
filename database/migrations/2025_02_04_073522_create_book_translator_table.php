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
        Schema::create('book_translator', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('translate_type_id');

            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_ku')->nullable();

            $table->foreign('translate_type_id')
            ->references('id')->on('translate_types')
            ->onDelete('cascade');  
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
        Schema::dropIfExists('book_translator');
    }
};
