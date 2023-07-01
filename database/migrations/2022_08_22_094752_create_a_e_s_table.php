<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAESTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('a_e_s', function (Blueprint $table) {
            $table->id();
            $table->boolean('blocked')->default(0);
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
        Schema::dropIfExists('a_e_s');
    }
}
