<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMeetablesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('meetables', function (Blueprint $table) {
            $table->foreign('meeting_id')->references('id')->on('meetings')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('meetables', function (Blueprint $table) {
            $table->dropForeign('meetables_meeting_id_foreign');
            $table->dropForeign('meetables_user_id_foreign');
        });
    }
}
