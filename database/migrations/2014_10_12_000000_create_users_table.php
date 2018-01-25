<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('google_id', 30)->unique();
            $table->string('language', 2)->default('en')->index();
            $table->string('theme', 20)->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('url')->nullable();
            $table->string('avatar')->nullable();
            $table->text('token')->nullable();
            $table->string('password', 60)->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
