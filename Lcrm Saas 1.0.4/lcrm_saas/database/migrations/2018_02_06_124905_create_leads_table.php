<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('assigned_partner_id')->nullable();
            $table->integer('organization_id');
            $table->integer('customer_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('company_site')->nullable();
            $table->string('opportunity')->nullable();
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('sales_person_id')->nullable();
            $table->integer('sales_team_id')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('function')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('tags')->nullable();
            $table->string('priority')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('leads');
    }
}
