<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasseMarksStoppedForSchoolYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('classe_marks_stopped_for_school_years', function (Blueprint $table) {
            $table->id();
            $table->string('semestre')->nullable()->default(null);
            
            $table->boolean('activated')->nullable()->default(true);
            $table->boolean('for_create')->nullable()->default(true);
            $table->boolean('for_delete')->nullable()->default(true);
            $table->boolean('for_update')->nullable()->default(true);

            $table->unsignedBigInteger('subject_id')->nullable()->default(null);
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('classe_id')->nullable();
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('level_id')->nullable()->default(null);
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('classe_marks_stopped_for_school_years');
    }
}
