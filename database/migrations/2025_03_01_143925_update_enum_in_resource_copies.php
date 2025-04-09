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
        Schema::table('resource_copies', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE `resource_copies` MODIFY COLUMN `status` ENUM('available','borrowed','reserved','lost','damaged') NOT NULL");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
