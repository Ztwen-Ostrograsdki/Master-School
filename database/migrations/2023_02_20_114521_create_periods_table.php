<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->date('start');
            $table->date('end');
            $table->string('object');
            $table->string('semestre')->nullable()->default(null);
            $table->boolean('blocked')->nullable()->default(false);
            $table->boolean('closed')->nullable()->default(false);
            $table->boolean('authorized')->nullable()->default(false);
            $table->string('target')->nullable()->default('semestre-trimestre');
            $table->string('description')->nullable()->default(null);
            $table->unsignedBigInteger('school_year_id')->nullable();
            $table->foreign('school_year_id')
                  ->on('school_years')
                  ->references('id')
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
        Schema::dropIfExists('periods');
    }
}
