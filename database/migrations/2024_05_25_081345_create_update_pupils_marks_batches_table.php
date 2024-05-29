<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdatePupilsMarksBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('update_pupils_marks_batches', function (Blueprint $table) {
            $table->id();
            $table->string('method_type')->default('insertion');
            $table->string('trimestre')->nullable()->default(null);
            $table->text('classes')->nullable()->default(null);
            $table->text('subjects')->nullable()->default(null);
            $table->text('types')->nullable()->default(null);
            $table->boolean('all_classes')->nullable()->default(false);
            $table->boolean('all_subjects')->nullable()->default(false);
            $table->boolean('all_semestres')->nullable()->default(false);
            $table->boolean('all_types')->nullable()->default(false);
            $table->text('description')->nullable()->default(null);
            $table->string('semestre')->nullable()->default(null);
            $table->unsignedBigInteger('total_marks')->nullable()->default(0);
            $table->boolean('finished')->default(false);

            $table->unsignedBigInteger('subject_id')->nullable()->default(null);
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('classe_id')->nullable()->default(null);
            $table->foreign('classe_id')
                  ->references('id')
                  ->on('classes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable()->default(null);
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

            $table->foreignUuid('batch_id')->nullable()->default(null);

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
        Schema::dropIfExists('update_pupils_marks_batches');
    }
}
