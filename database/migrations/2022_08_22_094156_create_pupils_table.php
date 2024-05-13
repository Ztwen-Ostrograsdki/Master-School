<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePupilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('pupils', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('matricule')->unique();
            $table->string('ltpk_matricule')->nullable()->default(rand(12225, 898788));
            $table->string('educmaster')->nullable()->default(null);
            $table->string('last_school_from')->nullable()->default(null);
            $table->string('contacts')->nullable()->default(null);
            $table->date('birth_day')->nullable()->default(null);
            $table->string('sexe')->nullable()->default(null);
            $table->string('residence')->nullable()->default(null);
            $table->string('birth_city')->nullable()->default(null);
            $table->string('nationality')->nullable()->default(null);
            $table->boolean('failed')->default(1);
            $table->boolean('authorized')->default(1);
            $table->boolean('abandonned')->default(0);
            $table->boolean('blocked')->default(0);
            $table->unsignedBigInteger('level_id')->nullable();
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
        Schema::dropIfExists('pupils');
    }
}
