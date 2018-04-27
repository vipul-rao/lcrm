<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfileIdToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('stripe_id')->nullable()->change();
            $table->string('stripe_plan')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->string('profile_id')->nullable();
            $table->string('subscription_type')->nullable();
            $table->string('status')->nullable();
            $table->integer('payplan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('stripe_id')->nullable(false)->change();
            $table->string('stripe_plan')->nullable(false)->change();
            $table->integer('quantity')->nullable(false)->change();
            $table->dropColumn('profile_id');
            $table->dropColumn('subscription_type');
            $table->dropColumn('status');
            $table->dropColumn('payplan_id');
        });
    }
}
