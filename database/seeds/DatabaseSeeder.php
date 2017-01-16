<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		if (App::environment() !== 'local') {

			throw new \Exception('Seeding only in local environment!');
			return;
		}

		Model::unguard();

		$this->call('ArticleCollectionSeeder');
		$this->call('UserTableSeeder');
		$this->call('ShopCollectionSeeder');
	}

}
