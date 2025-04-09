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



// <?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('resource_settings', function (Blueprint $table) {
//             $table->id();

//             $table->unsignedBigInteger('resource_id');
//             $table->boolean('availability')->default(1);
//             $table->integer('max_allowed_day')->default(7);
//             $table->boolean('allow_renewal')->default(0);
//             $table->integer('renewal_cycle')->nullable();
//             $table->boolean('locked')->default(0);

//             $table->foreign('resource_id')
//             ->references('id')->on('resources')
//             ->onDelete('cascade');

//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('resource_settings');
//     }
// };
