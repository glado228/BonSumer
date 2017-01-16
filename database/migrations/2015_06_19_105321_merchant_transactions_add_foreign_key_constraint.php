<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MerchantTransactionsAddForeignKeyConstraint extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('merchant_transactions', function(Blueprint $table)
		{
			$table->foreign('user_id')->references('id')
			->on('users')->onUpdate('restrict')->onDelete('restrict');

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
			$table->dropForeign('merchant_transactions_user_id_foreign');
		});
	}

}
