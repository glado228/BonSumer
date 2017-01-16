<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesCollectionUrlFriendlyTitleIndex extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mongodb')->table('articles', function(Blueprint $collection) {

			$collection->index('url_friendly_title');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mongodb')->table('articles', function(Blueprint $collection) {

			$collection->dropIndex('url_friendly_title');
		});
	}

}
