<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCallablesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('callables', function (Blueprint $table) {
            $table->foreign('call_id')->references('id')->on('calls')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('callables', function (Blueprint $table) {
            $table->dropForeign('callables_call_id_foreign');
            $table->dropForeign('callables_user_id_foreign');
        });
    }
}
