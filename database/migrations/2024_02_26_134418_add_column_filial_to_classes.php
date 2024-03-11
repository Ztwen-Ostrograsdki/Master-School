<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFilialToClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedBigInteger('filial_id')->nullable()->default(null);
            $table->foreign('filial_id')
                  ->references('id')
                  ->on('filials')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
        });

        Schema::table('classe_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('filial_id')->nullable()->default(null);
            $table->foreign('filial_id')
                  ->references('id')
                  ->on('filials')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            //
        });
    }
}
