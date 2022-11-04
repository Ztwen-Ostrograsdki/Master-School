<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherAbsencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('teacher_absences');
        Schema::disableForeignKeyConstraints();
        Schema::create('teacher_absences', function (Blueprint $table) {
            $table->id();
            $table->boolean('justified')->default(false);
            $table->date('date')->nullable()->default(null);
            $table->string('horaire')->nullable()->default(null);
            $table->string('school_year')->nullable()->default(null);
            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table->string('motif')->default('non justifiée');
            $table->string('trimestre')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')
                        ->references('id')
                        ->on('subjects')
                        ->onUpdate('cascade')
                        ->onDelete('cascade');
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');  
            $table->unsignedBigInteger('classe_id');
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
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
        Schema::dropIfExists('teacher_absences');
    }
}
