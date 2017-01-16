<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantTransactionsAddRewardFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_transactions', function(Blueprint $table)
		{
			$table->integer('reward')->unsigned()->default(0);
			$table->integer('reward_type')->unsinged()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('merchant_transactions', function(Blueprint $table)
		{
			$table->dropColumn('reward');
			$table->dropColumn('reward_type');
		});
	}

}
