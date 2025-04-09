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
        Schema::table('curators', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE `curators` MODIFY COLUMN `type` ENUM('author','creator','discussion_commetee','first_supervisor','second_supervisor','third_supervisor','researcher','supervisor','co-researcher','co_researcher','contributer') NOT NULL");
            DB::statement("UPDATE `curators` SET `type` = 'co_researcher' WHERE `type` = 'co-researcher'");
            DB::statement("ALTER TABLE `curators` MODIFY COLUMN `type` ENUM('author','creator','discussion_commetee','first_supervisor','second_supervisor','third_supervisor','researcher','supervisor','co_researcher','contributer') NOT NULL");

            

        });
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


