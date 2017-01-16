<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantTransactionsAddOrginalAmount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_transactions', function(Blueprint $table)
		{
			$table->decimal('original_amount', 10, 2);
			$table->decimal('original_commission', 10, 2);
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
			$table->dropColumn('original_amount');
			$table->dropColumn('original_commission');
		});
	}

}
