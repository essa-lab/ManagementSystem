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
        Schema::create('editors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_id');

            $table->text('content');
            $table->enum('language',['en','ar','ku']);
            $table->enum('type',['abstract','note','summary']);

            $table->foreign('resource_id')
                  ->references('id')
                  ->on('resources')->onDelete('cascade');

            $table->index('type');
            $table->index('language');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editors');
    }
};
