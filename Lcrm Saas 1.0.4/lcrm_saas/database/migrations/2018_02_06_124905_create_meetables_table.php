<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetablesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('meetables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meetable_id');
            $table->string('meetable_type');
            $table->integer('meeting_id')->unsigned()->index('meetables_meeting_id_foreign');
            $table->integer('user_id')->unsigned()->index('meetables_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('meetables');
    }
}
