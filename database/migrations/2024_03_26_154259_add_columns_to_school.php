<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSchool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->unsignedBigInteger('users_counter')->nullable()->default(null);
            $table->unsignedBigInteger('parents_counter')->nullable()->default(null);
            $table->unsignedBigInteger('pupils_counter')->nullable()->default(null);
            $table->unsignedBigInteger('classes_counter')->nullable()->default(null);
            $table->unsignedBigInteger('teachers_counter')->nullable()->default(null);
            $table->unsignedBigInteger('subjects_counter')->nullable()->default(null);
            $table->unsignedBigInteger('classe_groups_counter')->nullable()->default(null);
            $table->unsignedBigInteger('promotions_counter')->nullable()->default(null);
            $table->unsignedBigInteger('marks_counter')->nullable()->default(null);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school', function (Blueprint $table) {
            //
        });
    }
}
