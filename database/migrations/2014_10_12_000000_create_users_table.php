<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('firstname');
			$table->string('lastname');
			$table->char('gender', 1)->nullable();
			$table->string('email')->unique();
			$table->string('password', 60)->nullable();
			$table->boolean('admin')->default(FALSE);
			$table->boolean('confirmed')->default(FALSE);
			$table->string('confirmation_code')->nullable()->index();
			$table->timestamp('confirmation_code_creation');
			$table->string('reset_token')->nullable()->index();
			$table->timestamp('reset_token_creation');
			$table->rememberToken();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
