<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePayPlansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pay_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency');
            $table->string('interval');
            $table->string('plan_id');
            $table->string('amount');
            $table->string('name');
            $table->integer('no_people')->default(0);
            $table->string('statement_descriptor')->nullable();
            $table->integer('trial_period_days')->nullable();
            $table->boolean('is_credit_card_required')->nullable()->default(1);
            $table->integer('interval_count')->default(1);
            $table->integer('is_visible');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('pay_plans');
    }
}
