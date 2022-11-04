<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePupilCursusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('pupil_cursuses', function (Blueprint $table) {
            $table->id();
            $table->string('start')->nullable()->default(null);
            $table->string('end')->nullable()->default(null);
            $table->boolean('fullTime')->default(1);
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
                  ->onDelete('restrict')
                  ->onUpdate('restrict');    
            $table->unsignedBigInteger('pupil_id');
            $table->foreign('pupil_id')
                  ->references('id')
                  ->on('pupils')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');    
            $table->unsignedBigInteger('school_year_id');
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');    
            $table->softDeletes();
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
        Schema::dropIfExists('pupil_cursuses');
    }
}
