<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('company_avatar')->nullable();
            $table->string('company_attachment')->nullable();
            $table->integer('main_contact_person')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->integer('country_id')->unsigned();
            $table->integer('state_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('companies');
    }
}
