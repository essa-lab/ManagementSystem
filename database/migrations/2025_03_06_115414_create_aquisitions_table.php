<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('contact_number');
            $table->string('po_number')->unique();
            $table->date('date');
            $table->enum('status',['pending', 'shipped', 'received','gifted','canceled','other','approved'])->default('pending'); 
            $table->decimal('total_order_cost', 10, 2)->default(0.00);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('library_id')->nullable();

            $table->foreign('library_id')
            ->references('id')->on('libraries')
            ->onDelete('cascade');


            $table->unsignedBigInteger('approved_by')->nullable();

            $table->foreign('approved_by')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('created_by')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->timestamps();

        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->string('title');
            $table->enum('type',['Article','Book','Research','DigitalResource']);

            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
        });

        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->enum('status',['approved','pending','canceled', 'shipped', 'received','gifted','other'])->default('pending'); 
            $table->timestamp('changed_at')->useCurrent();

            $table->unsignedBigInteger('changed_by');

            $table->foreign('changed_by')
            ->references('id')->on('users')
            ->onDelete('cascade');
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
