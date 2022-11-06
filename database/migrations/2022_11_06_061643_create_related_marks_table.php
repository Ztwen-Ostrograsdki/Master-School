<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('related_marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('creator')->nullable()->default(null);
            $table->string('editor')->nullable()->default(null);
            $table->boolean('authorized')->default(false);
            $table->boolean('blocked')->default(false);
            $table->boolean('edited')->default(false);
            $table->boolean('forced_mark')->default(false);
            $table->float('value')->nullable();
            $table->date('date')->nullable()->default(null);
            $table->string('horaire')->nullable()->default(null);
            $table->float('editing_value')->nullable()->default(null);

            $table->string('type');
            $table->string('motif')->nullable()->default(null);
            $table->string('trimestre')->nullable()->default(null);
            $table->string('month')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);

            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')
                  ->references('id')
                  ->on('levels')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('pupil_id')->nullable();
            $table->foreign('pupil_id')
                  ->references('id')
                  ->on('pupils')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('classe_id')->nullable();
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
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
        Schema::dropIfExists('related_marks');
    }
}
