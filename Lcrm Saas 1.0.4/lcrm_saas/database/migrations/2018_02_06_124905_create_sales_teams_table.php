<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesTeamsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sales_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->string('salesteam');
            $table->integer('team_leader');
            $table->float('invoice_target', 15);
            $table->float('invoice_forecast', 15);
            $table->boolean('leads')->default(0);
            $table->boolean('quotations')->default(0);
            $table->boolean('opportunities')->default(0);
            $table->text('notes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('sales_teams');
    }
}
