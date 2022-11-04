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
            $table->string('school_year')->nullable()->default(null);
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('subject_id');
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
        Schema::dropIfExists('coeficients');
    }
}
