<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasseSanctionablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('classe_sanctionables', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('editor_id');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('school_year_id');
            $table->string('min')->nullable()->default(null);
            $table->string('max')->nullable()->default(null);
            $table->string('trimestre')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);
            $table->boolean('activated')->default(true);

            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 

            $table->foreign('creator_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('editor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 

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
        Schema::dropIfExists('classe_sanctionables');
    }
}
