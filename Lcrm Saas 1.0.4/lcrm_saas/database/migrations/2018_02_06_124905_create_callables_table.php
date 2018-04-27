<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCallablesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('callables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('call_id')->unsigned()->index('callables_call_id_foreign');
            $table->integer('user_id')->unsigned()->index('callables_user_id_foreign');
            $table->integer('callable_id');
            $table->string('callable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('callables');
    }
}
