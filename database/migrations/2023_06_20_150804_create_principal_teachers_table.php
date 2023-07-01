<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrincipalTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('principal_teachers', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('classe_id');
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 

            $table->unsignedBigInteger('teacher_id');
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
        Schema::dropIfExists('principal_teachers');
    }
}
