<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonetsCreditsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bonets_credits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('bonets');
			$table->datetime('date');
			$table->text('description')->nullable();

			$table->integer('user_id')->unsigned();

			$table->foreign('user_id')
			->references('id')->on('users')
			->onUpdate('cascade')
			->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bonets_credits');
	}

}
