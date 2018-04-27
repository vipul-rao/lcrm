<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id');
            $table->integer('company_id')->nullable();
            $table->string('date')->nullable();
            $table->text('call_summary')->nullable();
            $table->integer('duration')->nullable();
            $table->string('company_name')->nullable();
            $table->integer('resp_staff_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('calls');
    }
}
