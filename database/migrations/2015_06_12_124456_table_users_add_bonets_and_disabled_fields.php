<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableUsersAddBonetsAndDisabledFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->integer('bonets')->default(0);
			$table->boolean('disabled')->default(FALSE);
			$table->timestamp('disabled_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('bonets');
			$table->dropColumn('disabled');
			$table->dropColumn('disabled_at');
		});
	}

}
