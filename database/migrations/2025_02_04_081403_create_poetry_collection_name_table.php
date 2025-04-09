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
        Schema::create('poetry_collection_names', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('poetry_collection_id');

            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_ku')->nullable();

            $table->foreign('poetry_collection_id')
            ->references('id')->on('poetry_collections')
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
        Schema::dropIfExists('poetry_collection_name');
    }
};
