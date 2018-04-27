<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->string('user_avatar')->nullable();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->string('remember_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('users');
    }
}
