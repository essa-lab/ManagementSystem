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
        Schema::create('circulations', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('resource_copy_id');
            $table->unsignedBigInteger('patron_id');
            $table->timestamp('borrow_date')->nullable();
            $table->timestamp('return_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->integer('circulation_count')->default(0);
            $table->enum('status',['pending','rejected','borrowed','returned','overdue']);


            $table->foreign('resource_copy_id')
            ->references('id')->on('resource_copies')
            ->onDelete('cascade');
            
            $table->foreign('patron_id')
            ->references('id')->on('patrons')
            ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulations');
    }
};
