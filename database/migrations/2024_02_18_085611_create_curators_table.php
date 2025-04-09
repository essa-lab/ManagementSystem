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
        Schema::create('curators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_id');

            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ku')->nullable();

            $table->enum('type',['author','creator','discussion_commetee','first_supervisor','second_supervisor','third_supervisor']);

            $table->foreign('resource_id')
                  ->references('id')
                  ->on('resources')->onDelete('cascade');

            $table->index('type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curators');
    }
};
