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
        Schema::create('resource_sources', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('resource_id');
            
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_ku')->nullable();
            
            $table->foreign('source_id')
                  ->references('id')
                  ->on('sources')->onDelete('cascade');
            $table->foreign('resource_id')
                  ->references('id')
                  ->on('resources')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_sources');
    }
};
