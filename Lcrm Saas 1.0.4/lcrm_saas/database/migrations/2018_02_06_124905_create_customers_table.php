<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('company_id');
            $table->integer('organization_id')->nullable();
            $table->integer('belong_user_id')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->text('address');
            $table->string('website')->nullable();
            $table->string('job_position');
            $table->string('mobile');
            $table->string('fax')->nullable();
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('customers');
    }
}
