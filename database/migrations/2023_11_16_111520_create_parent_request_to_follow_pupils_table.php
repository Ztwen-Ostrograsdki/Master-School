<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentRequestToFollowPupilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::disableForeignKeyConstraints();
        Schema::create('parent_request_to_follow_pupils', function (Blueprint $table) {
            $table->id();
            $table->boolean('authorized')->default(0);
            $table->boolean('refused')->default(0);
            $table->boolean('analysed')->default(0);
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
        Schema::dropIfExists('parent_request_to_follow_pupils');
    }
}
