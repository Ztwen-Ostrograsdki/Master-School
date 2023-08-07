<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpreuvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epreuves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('authorized')->default(false);
            $table->boolean('blocked')->default(false);

            $table->string('name');
            $table->string('target')->nullable()->default('devoir');
            $table->text('description')->nullable()->default(null);
            $table->string('session')->nullable()->default(null);
            $table->string('exam_name')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);

            $table->unsignedBigInteger('level_id')->nullable()->default(null);
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('subject_id')->nullable()->default(null);
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('classe_group_id')->nullable()->default(null);
            $table->foreign('classe_group_id')
                  ->references('id')
                  ->on('classe_groups')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('author')->nullable()->default(null);

            $table->unsignedBigInteger('teacher_id')->nullable()->default(null);
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('classe_id')->nullable()->default(null);
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
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
        Schema::dropIfExists('epreuves');
    }
}
