<?php

use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Bonsum\MongoDB\Shop;
use \Carbon\Carbon;

class ShopCollectionSeeder extends Seeder {


	const SHOPS = 20;

	static $images = [
		'/home/bonsum_family_round.png',
		'/home/collect_bonets_round.png',
		'/home/do_good_round.png'
	];


	public function run() {

		$faker = FakerFactory::create();

		DB::connection('mongodb')->table('shops')->delete();

		for ($i = 0; $i < self::SHOPS; ++$i) {

			$vouchers = [];
			$voucher_count = $faker->numberBetween(5,10);
			for ($j = 0; $j < $voucher_count; ++$j) {

				$code_count = $faker->numberBetween(10,100);
				$codes = [];
				for ($k = 0; $k < $code_count; ++$k) {
					$codes[] = $faker->creditCardNumber;
				}
				$vouchers[] = [
					'codes' => $codes,
					'value' => $faker->randomElement([5, 10, 15])
				];
			}

			$shop_criteria = [];
			foreach (array_keys(Shop::$shopCriteria) as $cr) {
				$shop_criteria[$cr] = $faker->boolean(50);
			}

			$shop_type = [];
			foreach (array_keys(Shop::$shopTypes) as $type) {
				$shop_type[$type] = $faker->boolean(50);
			}

			$thumbnail = $faker->numberBetween(0,2);
			Shop::create([
				'locale' => App::getLocale(),
				'language' => app('localization')->getLang(),
				'visible' => $faker->boolean(80),
				'description' => $faker->paragraph(1),
				'name' => $faker->sentence(2),
				'thumbnail' => self::$images[$thumbnail],
				'thumbnail_mouseover' => self::$images[($thumbnail+1)%3],
				'shop_id' => $i+1,
				'shop_type' => $shop_type,
				'popularity' => $faker->numberBetween(0,100),
				'shop_criteria' => $shop_criteria,
				'vouchers' => $vouchers,
				'reward_type' => $faker->randomElement([Shop::REWARD_TYPE_PROPORTIONAL, Shop::REWARD_TYPE_FIXED]),
				'proportional_reward' => $faker->numberBetween(10,200),
				'fixed_reward' => $faker->numberBetween(10,10000)
			]);
		}
	}
}
