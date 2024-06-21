<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkActionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark_action_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mark_index')->nullable()->default(null);
            $table->float('value')->nullable();

            $table->string('type');
            $table->string('session')->nullable()->default(null);
            $table->string('exam_name')->nullable()->default(null);
            $table->string('trimestre')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);
            $table->string('action')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);

            $table->unsignedBigInteger('mark_id')->nullable()->default(null);

            $table->unsignedBigInteger('subject_id')->nullable()->default(null);
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('pupil_id')->nullable()->default(null);;
            $table->foreign('pupil_id')
                  ->references('id')
                  ->on('pupils')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');


            $table->unsignedBigInteger('classe_id')->nullable()->default(null);;
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable()->default(null);;
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('school_year_id')->nullable()->default(null);
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('mark_action_histories');
    }
}
