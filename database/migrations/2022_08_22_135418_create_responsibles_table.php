<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsiblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('responsibles', function (Blueprint $table) {
            $table->id();
            $table->string('rank')->nullable()->default(null);
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
        Schema::dropIfExists('responsibles');
    }
}
