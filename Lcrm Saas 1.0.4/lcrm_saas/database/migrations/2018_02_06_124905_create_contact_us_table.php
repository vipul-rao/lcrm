<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactUsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->text('message');
            $table->integer('from')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('contact_us');
    }
}
