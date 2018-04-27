<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesTeamMembersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sales_team_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('salesteam_id');
            $table->unique(['user_id', 'salesteam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('sales_team_members');
    }
}
