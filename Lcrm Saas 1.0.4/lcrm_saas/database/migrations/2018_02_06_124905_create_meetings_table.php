<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->text('meeting_subject');
            $table->integer('responsible_id');
            $table->dateTime('starting_date');
            $table->dateTime('ending_date');
            $table->boolean('all_day')->default(0);
            $table->string('location');
            $table->string('meeting_description');
            $table->string('privacy');
            $table->string('show_time_as');
            $table->string('duration')->nullable();
            $table->string('company_attendees')->nullable();
            $table->string('staff_attendees')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('meetings');
    }
}
