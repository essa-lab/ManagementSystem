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
        Schema::create('circulation_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('circulation_id');
            $table->unsignedBigInteger('action_by')->nullable();
            $table->timestamp('action_date')->nullable();

            
            $table->enum('status',['request_submitted','request_approved','request_rejected','borrowed','returned',
            'request_renew','renewed','renew_rejected','renew_accepted']);

            

            $table->foreign('circulation_id')
            ->references('id')->on('circulations')
            ->onDelete('cascade');
            
            $table->foreign('action_by')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulation_logs');
    }
};
