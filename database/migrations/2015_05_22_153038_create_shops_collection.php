<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsCollection extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mongodb')->create('shops', function(Blueprint $collection)
		{
			$collection->unique('shop_id');
			$collection->index('shop_criteria');
			$collection->index('shop_type');
			$collection->index('popularity');

			$collection->index(
				[
					'description' => 'text',
					'name' => 'text',
					'tags.text' => 'text'
				],
				[ 'weights' => [
						'name' => 10,
						'tags.text' => 10,
						'description' => 5
				]]
			);
			// create a text index and all other needed indexes here
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mongodb')->drop('shops');
	}

}
