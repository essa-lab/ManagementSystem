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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('circulation_id');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->string('how_much_per_day')->nullable();
            $table->string('days_overdue')->nullable();
            $table->boolean('is_paid')->nullable();
            $table->string('total_penalty_amount')->nullable();

            $table->foreign('circulation_id')
            ->references('id')->on('circulations')
            ->onDelete('cascade');
            
            $table->foreign('updated_by')
            ->references('id')->on('users')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
