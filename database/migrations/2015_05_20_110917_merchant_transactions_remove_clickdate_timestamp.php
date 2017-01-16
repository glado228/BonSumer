<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantTransactionsRemoveClickdateTimestamp extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_transactions', function(Blueprint $table)
		{
			$table->dropColumn('clickdate');
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
			$table->timestamp('clickdate');
		});
	}

}
