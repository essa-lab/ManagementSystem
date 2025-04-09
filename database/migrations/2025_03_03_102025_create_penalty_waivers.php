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
        Schema::create('penalty_waivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penlaty_id');
            $table->unsignedBigInteger('waived_by');
            $table->text('reason');

            $table->foreign('waived_by')
            ->references('id')->on('users')
            ->onDelete('cascade');
            
            $table->foreign('penlaty_id')
            ->references('id')->on('penalties')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalty_waivers');
    }
};
