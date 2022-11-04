<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('teachers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contacts')->nullable()->default(null);
            $table->string('nationality')->nullable()->default(null);
            $table->string('marital_status')->nullable()->default(null);

            $table->date('birth_day')->nullable()->default(null);
            $table->string('residence')->nullable()->default(null);
            $table->boolean('authorized')->default(false);
            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict'); 

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
        Schema::dropIfExists('teachers');
    }
}
