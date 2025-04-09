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
        Schema::create('digital_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_format_id');
            $table->unsignedBigInteger('digital_resource_type_id');

            $table->string('identifier')->nullable();
            $table->string('coverage')->nullable();
            $table->string('publisher')->nullable();

            $table->foreign('digital_format_id')
                  ->references('id')
                  ->on('digital_formats')->onDelete('cascade');
            $table->foreign('digital_resource_type_id')
                  ->references('id')
                  ->on('digital_resource_types')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('degital_resources');
    }
};
