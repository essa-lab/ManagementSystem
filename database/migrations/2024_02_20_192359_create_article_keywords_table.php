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
        Schema::create('article_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_ku')->nullable();

            $table->foreign('article_id')
            ->references('id')->on('articles')
            ->onDelete('cascade');  
                      
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_keywords');
    }
};
