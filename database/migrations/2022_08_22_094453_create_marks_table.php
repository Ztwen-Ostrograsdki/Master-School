<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('creator')->nullable()->default(null);
            $table->string('editor')->nullable()->default(null);
            $table->boolean('authorized')->default(false);
            $table->boolean('forget')->default(false);
            $table->unsignedBigInteger('mark_index')->nullable()->default(null);
            $table->boolean('blocked')->default(false);
            $table->boolean('edited')->default(false);
            $table->boolean('forced_mark')->default(false);
            $table->float('value')->nullable();
            $table->boolean('updating')->default(false);
            $table->float('editing_value')->nullable()->default(null);

            $table->string('type');
            $table->string('session')->nullable()->default(null);
            $table->string('exam_name')->nullable()->default(null);
            $table->string('trimestre')->nullable()->default(null);
            $table->string('month')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);

            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('pupil_id')->nullable();
            $table->foreign('pupil_id')
                  ->references('id')
                  ->on('pupils')
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

            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marks');
    }


    
}
