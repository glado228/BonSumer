<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonetsRedeemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bonets_redeems', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();

			$table->integer('bonets');
			$table->decimal('amount', 10, 2);
			$table->char('currency', 3);
			$table->string('voucher_code');
			$table->datetime('date');

			$table->integer('user_id')->unsigned();

			$table->foreign('user_id')
			->references('id')->on('users')
			->onUpdate('cascade')
			->onDelete('restrict');

			$table->string('shop_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bonets_redeems');
	}

}
