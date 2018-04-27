<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('fax')->nullable();
            $table->string('logo')->nullable();
            $table->integer('user_id')->unsigned();
            $table->string('stripe_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->integer('generic_trial_plan')->nullable();
            $table->integer('is_deleted')->default(0);
            $table->boolean('created_by_admin')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('organizations');
    }
}
