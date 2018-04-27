<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assign_customer_id')->nullable();
            $table->string('to');
            $table->string('from');
            $table->string('subject');
            $table->text('message');
            $table->boolean('read')->default(0);
            $table->boolean('delete_sender')->default(0);
            $table->boolean('delete_receiver')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('emails');
    }
}
