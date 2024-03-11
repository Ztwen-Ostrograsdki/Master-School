<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnFilialIdFromClasses extends Migration
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
            $table->dropForeign('classes_filial_id_foreign');
            $table->dropColumn('filial_id');
        });

        Schema::table('classe_groups', function (Blueprint $table) {
            $table->dropForeign('classe_groups_filial_id_foreign');
            $table->dropColumn('filial_id');
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
