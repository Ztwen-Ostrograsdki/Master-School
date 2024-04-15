<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('trimestre')->default(0);
            $table->boolean('semestre')->default(1);
            $table->unsignedBigInteger('users_counter')->nullable()->default(null);
            $table->unsignedBigInteger('parents_counter')->nullable()->default(null);
            $table->unsignedBigInteger('pupils_counter')->nullable()->default(null);
            $table->unsignedBigInteger('classes_counter')->nullable()->default(null);
            $table->unsignedBigInteger('teachers_counter')->nullable()->default(null);
            $table->unsignedBigInteger('subjects_counter')->nullable()->default(null);
            $table->unsignedBigInteger('classe_groups_counter')->nullable()->default(null);
            $table->unsignedBigInteger('promotions_counter')->nullable()->default(null);
            $table->unsignedBigInteger('marks_counter')->nullable()->default(null);
            $table->unsignedBigInteger('epreuves_counter')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools');
    }
}
