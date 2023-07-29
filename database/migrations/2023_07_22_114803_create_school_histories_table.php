<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_histories', function (Blueprint $table) {
            $table->id();
            $table->boolean('seen')->default(0);
            $table->string('visibility')->nullable()->default('users');
            $table->string('table')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->text('content')->nullable()->default(null);
            $table->unsignedBigInteger('model_id')->nullable()->default(null);
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
        Schema::dropIfExists('school_histories');
    }
}
