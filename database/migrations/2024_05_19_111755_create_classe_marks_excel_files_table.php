<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasseMarksExcelFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    // ['name', 'path', 'classe_id', 'subject_id', 'school_year_id', 'semestre', 'user_id', 'downloaded', 'downloaded_counter', 'secure'];

    public function up()
    {
        Schema::create('classe_marks_excel_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('school_year_id');
            $table->string('name');
            $table->string('extension');
            $table->string('path')->nullable()->default(null);
            $table->string('trimestre')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);
            $table->unsignedBigInteger('downloaded_counter')->nullable()->default(0);
            $table->boolean('downloaded')->default(false);
            $table->boolean('secure')->default(false);
            $table->boolean('locked')->default(false);

            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('restrict')
                  ->onUpdate('restrict'); 

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('restrict')
                  ->onUpdate('restrict'); 

            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('restrict')
                  ->onUpdate('restrict'); 
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
        Schema::dropIfExists('classe_marks_excel_files');
    }
}
