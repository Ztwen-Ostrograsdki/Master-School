<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use function PHPUnit\Framework\once;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('pseudo')->nullable()->default('User');
            $table->string('email')->unique();
            $table->string('new_email')->nullable()->default(null);
            
            $table->unsignedBigInteger('role_id')->default(1);
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('restrict')
                  ->onUpdate('restrict');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('token')->nullable()->default(null);
            $table->string('unlock_token')->nullable()->default(null);
            $table->boolean('blocked')->nullable()->default(false);
            $table->boolean('locked')->default(false);
            $table->string('school_year')->nullable()->default(null);
            $table->string('email_verified_token')->nullable()->default(null);
            $table->string('new_email_verified_token')->nullable()->default(null);
            $table->string('reset_password_token')->nullable()->default(null);
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
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
        Schema::dropIfExists('users');
    }
};
