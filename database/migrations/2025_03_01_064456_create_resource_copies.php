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
        Schema::create('resource_copies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_id');

            $table->integer('copy_number')->nullable();
            $table->string('barcode')->nullable();
            $table->string('shelf_number')->nullable();
            $table->string('storage_location')->nullable();
            $table->enum('status',['available','borrowed','reserved','lost','damaged'])->default('available');


            $table->foreign('resource_id')
            ->references('id')->on('resources')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_copies');
    }
};
