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
        DB::statement("ALTER TABLE `curators` MODIFY COLUMN `type` ENUM('author','creator','discussion_commetee','first_supervisor','second_supervisor','third_supervisor','researcher','supervisor','co-researcher','contributer') NOT NULL");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curators', function (Blueprint $table) {
            //
        });
    }
};
