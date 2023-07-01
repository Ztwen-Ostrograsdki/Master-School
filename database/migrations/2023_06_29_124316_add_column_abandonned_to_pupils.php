<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAbandonnedToPupils extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pupils', function (Blueprint $table) {
            $table->boolean('abandonned')->default(0);
        });


        Schema::table('marks', function (Blueprint $table1) {
            $table1->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table1->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

        Schema::table('related_marks', function (Blueprint $table2) {
            $table2->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table2->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pupils', function (Blueprint $table) {
            //
        });
    }
}
