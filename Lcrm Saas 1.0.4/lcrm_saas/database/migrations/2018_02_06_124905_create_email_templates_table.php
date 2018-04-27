<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('organization_id')->nullable();
            $table->string('title');
            $table->text('text');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('email_templates');
    }
}
