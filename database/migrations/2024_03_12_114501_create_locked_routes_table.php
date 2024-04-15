<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLockedRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('locked_routes', function (Blueprint $table) {
            $table->id();
            $table->boolean('activated')->default(1);
            $table->date('expired_date')->nullable()->default(null);
            $table->string('delay')->nullable()->default(null);
            $table->string('url')->nullable()->default(null);
            $table->string('path')->nullable()->default(null);
            $table->string('routeName')->nullable()->default(null);
            $table->string('targeted_users')->nullable()->default(null);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
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
        Schema::dropIfExists('locked_routes');
    }
}
