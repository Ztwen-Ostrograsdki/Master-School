<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesSecuritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('classes_securities');
        Schema::create('classes_securities', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable()->default(null);
            $table->boolean('activated')->default(1);
            $table->boolean('locked_classe')->default(0);
            $table->boolean('locked_marks')->default(0);
            $table->boolean('closed_classe')->default(0);
            $table->boolean('locked_marks_updating')->default(0);
            $table->boolean('closed')->default(0);
            $table->boolean('locked')->default(0);

            $table->unsignedBigInteger('duration')->nullable()->default(48);
            $table->unsignedBigInteger('level_id')->nullable()->default(null);
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('classe_id');
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');  

            $table->unsignedBigInteger('pupil_id')->nullable()->default(null);
            $table->foreign('pupil_id')
                  ->references('id')
                  ->on('pupils')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');     
                   
            $table->unsignedBigInteger('teacher_id')->nullable()->default(null);
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');    

            $table->unsignedBigInteger('school_year_id');
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');   

            $table->unsignedBigInteger('subject_id')->nullable()->default(null);
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');   

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
        Schema::dropIfExists('classes_securities');
    }
}
