<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamp('date')->index();
			$table->string('title');
			$table->text('description');
			$table->string('authors', 600)->nullable();
			$table->string('tags', 600);
			$table->text('body');
			$table->string('image')->nullable();
			$table->string('locale')->default('en');
			$table->boolean('visible')->default(0);
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
		Schema::drop('articles');
	}

}
