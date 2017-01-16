<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesCollectionTextIndexes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mongodb')->table('articles', function(Blueprint $collection)
		{
			$collection->dropIndex('tags');
			$collection->index(
				[
					'$**' => 'text'
				]
				,['name' => 'articles_text_index']
			);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mongodb')->table('articles', function(Blueprint $collection)
		{
		// could not find a way to delete text indexes from here :(
		});
	}

}
