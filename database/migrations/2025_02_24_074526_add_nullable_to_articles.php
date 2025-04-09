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
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('article_scientific_classification_id')->nullable()->change();
            $table->unsignedBigInteger('article_specification_id')->nullable()->change();
            $table->unsignedBigInteger('article_type_id')->nullable()->change();
            $table->string('registeration_number')->nullable()->change();
        });

        Schema::table('books', function (Blueprint $table) {
            $table->string('registeration_number')->nullable()->change();
        });

        Schema::table('digital_resources', function (Blueprint $table) {
            $table->unsignedBigInteger('digital_format_id')->nullable()->change();
            $table->unsignedBigInteger('digital_resource_type_id')->nullable()->change();
        });
        Schema::table('researches', function (Blueprint $table) {
            $table->unsignedBigInteger('education_level_id')->nullable()->change();
            $table->unsignedBigInteger('research_type_id')->nullable()->change();
            $table->unsignedBigInteger('research_format_id')->nullable()->change();
            $table->string('registeration_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            //
        });
    }
};
