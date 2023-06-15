<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('creator')->nullable()->default(null);
            $table->string('editor')->nullable()->default(null);
            $table->boolean('authorized')->default(true);
            $table->boolean('closed')->default(false);
            $table->unsignedBigInteger('start');
            $table->unsignedBigInteger('end');
            $table->unsignedBigInteger('duration');
            $table->string('description')->nullable()->default(null);
            $table->string('day');
            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);

            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('classe_id')->nullable();
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

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
        Schema::dropIfExists('time_plans');
    }
}
