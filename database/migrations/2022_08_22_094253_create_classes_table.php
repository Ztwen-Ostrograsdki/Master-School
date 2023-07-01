<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable()->default(null);
            $table->boolean('closed')->default(false);
            $table->boolean('locked')->default(false);
            $table->unsignedBigInteger('position')->nullable()->default(null);

            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('classe_group_id')->nullable();
            $table->foreign('classe_group_id')
                  ->references('id')
                  ->on('classe_groups')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
