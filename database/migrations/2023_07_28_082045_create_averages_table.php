<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('averages', function (Blueprint $table) {
            $table->id();
            $table->float('moy')->nullable()->default(null);
            $table->float('max')->nullable()->default(null);
            $table->float('min')->nullable()->default(null);
            $table->string('mention')->nullable()->default(null);
            $table->string('base')->nullable()->default(null);
            $table->string('exp')->nullable()->default(null);
            $table->string('rank')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);

            $table->unsignedBigInteger('pupil_id');
            $table->foreign('pupil_id')
                        ->references('id')
                        ->on('pupils')
                        ->onUpdate('cascade')
                        ->onDelete('cascade');

            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
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
        Schema::dropIfExists('averages');
    }
}
