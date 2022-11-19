<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoeficientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('coeficients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coef')->default(1);
            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('classe_group_id');
            $table->unsignedBigInteger('subject_id');
            $table->foreign('classe_group_id')
                  ->references('id')
                  ->on('classe_groups')
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
        Schema::dropIfExists('coeficients');
    }
}
