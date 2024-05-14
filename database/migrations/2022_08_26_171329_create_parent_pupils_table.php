<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentPupilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('parent_pupils', function (Blueprint $table) {
            $table->id();
            $table->boolean('locked')->default(false);
            $table->string('relation')->nullable()->default('Parenté');
            $table->unsignedBigInteger('parentable_id');
            $table->foreign('parentable_id')
                  ->references('id')
                  ->on('parentables')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');    
            $table->unsignedBigInteger('pupil_id');
            $table->foreign('pupil_id')
                  ->references('id')
                  ->on('pupils')
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
        Schema::dropIfExists('parent_pupils');
    }
}
