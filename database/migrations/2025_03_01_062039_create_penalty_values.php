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
        Schema::create('penalty_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->integer('amount');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')->onDelete('cascade');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalty_values');
    }
};
