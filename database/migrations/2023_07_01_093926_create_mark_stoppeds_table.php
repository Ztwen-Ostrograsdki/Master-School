<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkStoppedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('mark_stoppeds', function (Blueprint $table) {
            $table->id();
            $table->boolean('stopped')->default(1);
            $table->string('semestre')->nullable()->default(null);

            $table->unsignedBigInteger('level_id')->nullable()->default(null);
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('mark_stoppeds');
    }
}
