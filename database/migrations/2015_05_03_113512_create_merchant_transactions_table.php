<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('network');
			$table->string('network_tid'); // transaction id assigned by the network
			$table->unique(['network', 'network_tid']);
			$table->char('currency', 3)->nullable();
			$table->decimal('amount', 10, 2);
			$table->decimal('commission', 10, 2);
			$table->string('network_status'); // status of transaction as communicated from the network
			$table->integer('status_override');
			$table->integer('internal_status'); // status we regard the transaction to be in. Takes the same values as status_override
			$table->integer('program_id');
			$table->string('program_name');
			$table->timestamp('clickdate');
			$table->integer('shop_id')->unsigned()->nullable();
			$table->integer('user_id')->unsigned()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_transactions');
	}

}
