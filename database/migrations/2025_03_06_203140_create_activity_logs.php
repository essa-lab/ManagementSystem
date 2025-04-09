<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('activity_logs', function (Blueprint $table) {

            $table->id();
            $table->string('loggable_type'); 
            $table->unsignedBigInteger('loggable_id'); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changes')->nullable(); 
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('performed_at')->useCurrent();

            $table->timestamps();
            
            $table->index(['loggable_type', 'loggable_id']);
            $table->index('user_id');
        });
    }

    public function down() {
    }
};
