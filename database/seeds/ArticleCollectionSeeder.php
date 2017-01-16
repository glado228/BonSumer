<?php

use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Bonsum\MongoDB\Article as MDBArticle;
use \Carbon\Carbon;

class ArticleCollectionSeeder extends Seeder {


	/**
	 * how many articles
	 */
	const ARTICLES = 50;

	static $images = [
		'/home/bonsum_family_round.png',
		'/home/collect_bonets_round.png',
		'/home/do_good_round.png'
	];

	public function run() {

		DB::connection('mongodb')->table('articles')->delete();
		DB::table('articles')->delete();

		$faker = FakerFactory::create();

		for ($i = 0; $i < self::ARTICLES; ++$i) {

			$authors_num = $faker->numberBetween(1,3);
			$authors = [];
			for ($k = 0; $k < $authors_num; ++$k) {
				$authors[] = [
					'text' => $faker->firstName . ' ' . $faker->lastName,
				];
			}

			$tags_num = $faker->numberBetween(1,6);
			$tags = [];
			for ($k = 0; $k < $tags_num; ++$k) {
				$tags[] = [
					'text' => $faker->word
				];
			}
/*
			Article::create([

				'visible' => $faker->boolean(70),
				'date' => $faker->dateTimeThisDecade(),
				'title' => $faker->sentence(6),
				'description' => $faker->paragraph(3),
				'body' => $faker->text(500),
				'authors' => $authors,
				'tags' => $tags,
				'image' => self::$images[$faker->numberBetween(0,2)]
			]);*/

			MDBArticle::create([

				'locale' => App::getLocale(),
				'language' => app('localization')->getLang(),
				'visible' => $faker->boolean(70),
				'date' => $faker->dateTimeThisDecade(),
				'title' => $faker->sentence(6),
				'description' => $faker->paragraph(3),
				'body' => $faker->text(500),
				'authors' => $authors,
				'tags' => $tags,
				'image' => self::$images[$faker->numberBetween(0,2)]
			]);
		};
	}

}
