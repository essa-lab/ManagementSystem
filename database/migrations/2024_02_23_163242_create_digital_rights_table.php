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
        Schema::create('digital_rights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_resource_id');
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_ku')->nullable();

            $table->foreign('digital_resource_id')
            ->references('id')->on('digital_resources')
            ->onDelete('cascade');  
                      
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_rights');
    }
};
