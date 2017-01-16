<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDonationsCollection extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection('mongodb')->create('donations', function(Blueprint $collection)
		{
			$collection->index('popularity');
			$collection->index(
				[
					'description' => 'text',
					'name' => 'text',
					'tags.text' => 'text'
				],
				[ 'weights' => [
						'tags.text' => 10,
						'name' => 10,
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
		Schema::connection('mongodb')->drop('donations');
	}

}
