<?php


use Bonsum\User;
use Carbon\Carbon;
use Bonsum\MongoDB\Shop;
use Faker\Factory as FakerFactory;


class ShopTest extends TestCase {


	public function setUp() {

		parent::setUp();
		$this->app->make('session')->start();
		$faker = FakerFactory::create();

		$vouchers = [];
		$this->values = [5,10,50];
		$voucher_count = $faker->numberBetween(1000,5000);
		foreach ($this->values as $value) {
			$codes = [];
			for ($j = 0; $j < $voucher_count; ++$j) {
				$codes[] = $faker->creditCardNumber;
			}
			$vouchers[] = [
				'codes' => $codes,
				'value' => $value
			];
		}

		$this->shop = Shop::create([
			'language' => $this->app['localization']->getLang(),
			'locale' => App::getLocale(),
			'visible' => false,
			'description' => $faker->paragraph(1),
			'name' => $faker->sentence(2),
			'shop_id' => $faker->numberBetween(10000,20000),
			'shop_type' => array_map([$faker, 'boolean'], Shop::$shopTypes),
			'popularity' => $faker->numberBetween(0,100),
			'shop_criteria' => array_map([$faker, 'boolean'], Shop::$shopCriteria),
			'vouchers' => $vouchers,
			'reward_type' => $faker->randomElement([Shop::REWARD_TYPE_PROPORTIONAL, Shop::REWARD_TYPE_FIXED]),
			'proportional_reward' => $faker->numberBetween(10,200),
			'fixed_reward' => $faker->numberBetween(10,10000)
		]);

		$this->faker = $faker;

	}

	public function tearDown() {

		$this->shop->delete();
	}


	public function testAddVoucher() {

		$user = new User();
		$user->admin = true;
		$this->be($user);

		$value = $this->faker->randomElement($this->values);
		$voucher = [
			'codes' => [str_random(10)],
			'value' => $value
		];

		$v_index = $this->shop->getIndexForVoucherValue($value);

		$this->action(
			'POST',
			'ShopController@addVouchers',
			[$this->shop->id],
			$voucher,
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(),  'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(200);
		$shop = Shop::find($this->shop->id);
		$codes = $shop->vouchers[$v_index]['codes'];
		$original_codes = $this->shop->vouchers[$v_index]['codes'];
		$this->assertContains($voucher['codes'][0], $codes);
		$this->assertEquals(count($original_codes)+1, count($codes));
	}


	public function testDeleteVoucher() {

		$user = new User();
		$user->admin = true;
		$this->be($user);

		$value = $this->faker->randomElement($this->values);
		$v_index = $this->shop->getIndexForVoucherValue($value);
		$original_codes = $this->shop->vouchers[$v_index]['codes'];
		$code = $original_codes[$this->faker->numberBetween(0,count($original_codes))-1];

		$this->action(
			'POST',
			'ShopController@deleteVoucher',
			[$this->shop->id],
			[
				'code' => $code,
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(),  'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(200);
		$shop = Shop::find($this->shop->id);
		$found = false;
		$codes = $shop->vouchers[$v_index]['codes'];
		foreach ($codes as $c) {
			if ($c == $code) {
				$found = true;
			}
		}
		$this->assertFalse($found);
		$this->assertEquals(count($original_codes)-1, count($codes));
	}

	public function testAddShopUnauthenticated() {

		$this->action(
			'POST',
			'ShopController@store',
			[],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(401);
	}


	public function testAddShopNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'POST',
			'ShopController@store',
			[],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(403);
	}

	public function testDeleteShopNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'DELETE',
			'ShopController@destroy',
			['id' => $this->shop->id],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(403);
	}

	public function testViewHiddenShopsNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'GET',
			'ShopController@indexInvisible'
		);

		$this->assertResponseStatus(403);
	}

	public function testEditShopNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'GET',
			'ShopController@edit',
			[$this->shop->id]
		);

		$this->assertResponseStatus(403);
	}

}
