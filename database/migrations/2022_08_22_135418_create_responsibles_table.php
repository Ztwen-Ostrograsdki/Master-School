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
        Schema::dropIfExists('responsibles');
        Schema::create('responsibles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rank')->nullable()->default(1);
            $table->unsignedBigInteger('classe_id');
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade'); 

            $table->unsignedBigInteger('respo_1')->nullable()->default(null);
            $table->unsignedBigInteger('respo_2')->nullable()->default(null);
            $table->unsignedBigInteger('respo_3')->nullable()->default(null);
            $table->unsignedBigInteger('respo_6')->nullable()->default(null);
            $table->unsignedBigInteger('respo_7')->nullable()->default(null);
            $table->unsignedBigInteger('respo_8')->nullable()->default(null);
            $table->unsignedBigInteger('respo_9')->nullable()->default(null);
            $table->unsignedBigInteger('respo_10')->nullable()->default(null);
            
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
        Schema::dropIfExists('responsibles');
    }
}
