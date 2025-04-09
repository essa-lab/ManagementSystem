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
        Schema::create('print_types_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('print_type_id');
            $table->foreign('print_type_id')
                  ->references('id')
                  ->on('print_types')->onDelete('cascade');

            $table->unsignedBigInteger('print_book_id');
            $table->foreign('print_book_id')
                  ->references('id')
                  ->on('print_books')->onDelete('cascade');
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('title_ku');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_types_pivot');
    }
};
