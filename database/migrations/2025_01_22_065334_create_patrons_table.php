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
        Schema::create('patrons', function (Blueprint $table) {
            $table->id();
            $table->string('internal_identifier');
            $table->string('name');
            $table->string('occupation')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('university')->nullable();
            $table->string('college')->nullable();

            $table->string('address')->nullable();
            $table->string('password');
            $table->string('profile_picture',255)->nullable();
            $table->rememberToken();
            $table->string('locale', 5)->default('en');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean( 'verified')->default(0);            
            $table->timestamp('last_login_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrons');
    }
};
