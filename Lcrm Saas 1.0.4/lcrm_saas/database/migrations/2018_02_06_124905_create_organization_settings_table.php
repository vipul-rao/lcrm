<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganizationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('organization_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('key');
            $table->text('value');
            $table->unique(['organization_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('organization_settings');
    }
}
