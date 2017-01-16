<?php

use Bonsum\User;
use Carbon\Carbon;
use Bonsum\MongoDB\Donation;
use Bonsum\MongoDB\Shop;
use Faker\Factory as FakerFactory;



class RedeemTest extends TestCase {


	public function setUp() {

		parent::setUp();

		Donation::unguard();
		Shop::unguard();
		User::unguard();
		$this->app->make('session')->start();
		$faker = FakerFactory::create();
		$this->faker = $faker;

		$this->donation_sizes = [5000, 10000, 100000];

		$this->donation = Donation::create([
			'langauge' => $this->app['localization']->getLang(),
			'locale' => App::getLocale(),
			'visible' => true,
			'description' => $faker->paragraph(1),
			'name' => $faker->sentence(2),
			'popularity' => $faker->numberBetween(0,100),
			'donation_sizes' => $this->donation_sizes
		]);

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
			'langauge' => $this->app['localization']->getLang(),
			'locale' => App::getLocale(),
			'visible' => true,
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

		$this->user = User::create([
			'firstname' => 'Max',
			'lastname' => 'Mustermann',
			'email' => 'nomail@nodomain.com',
			'password' => str_random(10),
			'bonets' => 100000,
			'preferred_locale' => 'de-DE'
		]);


		$this->bonets_service = $this->app->make('bonets');

	}

	public function tearDown() {

		$this->user->bonets_redeems->each(function($e) {
			$e->delete();
		});
		$this->user->bonets_donations->each(function($e) {
			$e->delete();
		});
		$this->user->delete();
		$this->donation->delete();
		$this->shop->delete();
	}

	public function testAddDonationNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'POST',
			'RedeemController@store',
			[],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(403);
	}

	public function testDeleteDonationNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'DELETE',
			'RedeemController@destroy',
			['id' => $this->donation->id],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(403);
	}


	public function testViewHiddenOptionsNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'GET',
			'RedeemController@indexInvisible'
		);

		$this->assertResponseStatus(403);
	}

	public function testEditDonationNoAdmin() {

		$user = new User();
		$this->be($user);

		$this->action(
			'GET',
			'RedeemController@edit',
			[$this->donation->id]
		);

		$this->assertResponseStatus(403);
	}

	public function testRedeemVoucher() {


		$this->be($this->user);

		$amount = $this->faker->randomElement($this->values);
		$bonets = $this->bonets_service->toBonets($amount);
		$vindex = $this->shop->getIndexForVoucherValue($amount);
		$voucher_codes = $this->shop->vouchers[$vindex]['codes'];
		$voucher_code = array_shift($voucher_codes);

		$this->action(
			'POST',
			'RedeemController@getVoucher',
			['id' => $this->shop->id],
			['amount' => $amount],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$shop = Shop::find($this->shop->id);
		$this->assertEquals($this->user->bonets - $bonets, $user->bonets);
		$this->assertEquals(count($shop->vouchers[$vindex]['codes']), count($voucher_codes));

		$redeem = $user->bonets_redeems->sortBy('date')->last();
		$this->assertEquals($shop->id, $redeem->shop_id);
		$this->assertEquals($user->id, $redeem->user_id);
		$this->assertEquals($bonets, $redeem->bonets);
		$this->assertEquals($amount, $redeem->amount);
		$this->assertEquals($voucher_code, $redeem->voucher_code);

	}

	public function testRedeemVoucherHiddenShop() {

		$this->be($this->user);
		$this->shop->visible = false;
		$this->shop->save();

		$amount = $this->faker->randomElement($this->values);
		$vindex = $this->shop->getIndexForVoucherValue($amount);
		$voucher_codes = $this->shop->vouchers[$vindex]['codes'];

		$this->action(
			'POST',
			'RedeemController@getVoucher',
			['id' => $this->shop->id],
			['amount' => $amount],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(500);
		$user = User::find($this->user->id);
		$shop = Shop::find($this->shop->id);
		$this->assertEquals($this->user->bonets, $user->bonets);
		$this->assertEquals(count($shop->vouchers[$vindex]['codes']), count($voucher_codes));
	}
	public function testRedeemVoucherNotLoggedIn() {


		$amount = $this->faker->randomElement($this->values);
		$vindex = $this->shop->getIndexForVoucherValue($amount);
		$voucher_codes = $this->shop->vouchers[$vindex]['codes'];

		$this->action(
			'POST',
			'RedeemController@getVoucher',
			['id' => $this->shop->id],
			['amount' => $amount],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(401);
		$user = User::find($this->user->id);
		$shop = Shop::find($this->shop->id);
		$this->assertEquals($this->user->bonets, $user->bonets);
		$this->assertEquals(count($shop->vouchers[$vindex]['codes']), count($voucher_codes));
	}


	public function testRedeemVoucherNotEnoughBonets() {


		$this->user->bonets = 100;
		$this->user->save();

		$amount = $this->faker->randomElement($this->values);
		$vindex = $this->shop->getIndexForVoucherValue($amount);
		$voucher_codes = $this->shop->vouchers[$vindex]['codes'];

		$this->be($this->user);

		$amount = $this->faker->randomElement($this->values);

		$this->action(
			'POST',
			'RedeemController@getVoucher',
			['id' => $this->shop->id],
			['amount' => $amount],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(422);
		$user = User::find($this->user->id);
		$shop = Shop::find($this->shop->id);
		$this->assertEquals($this->user->bonets, $user->bonets);
		$this->assertEquals(count($shop->vouchers[$vindex]['codes']), count($voucher_codes));
	}

	public function testDonate() {


		$this->be($this->user);
		$bonets = $this->faker->randomElement($this->donation_sizes);
		$amount = $this->bonets_service->fromBonets($bonets);

		$this->action(
			'POST',
			'RedeemController@donate',
			['id' => $this->donation->id],
			['bonets' => $bonets],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->bonets - $bonets, $user->bonets);

		$donation = $this->user->bonets_donations->sortBy('date')->last();
		$this->assertEquals($this->donation->id, $donation->donation_id);
		$this->assertEquals($user->id, $donation->user_id);
		$this->assertEquals($bonets, $donation->bonets);
		$this->assertEquals($amount, $donation->amount);
	}

	public function testDonateHiddenOption() {

		$this->be($this->user);

		$this->donation->visible = false;
		$this->donation->save();
		$bonets = $this->faker->randomElement($this->donation_sizes);
		$amount = $this->bonets_service->fromBonets($bonets);

		$this->action(
			'POST',
			'RedeemController@donate',
			['id' => $this->donation->id],
			['bonets' => $bonets],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(404);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->bonets, $user->bonets);
	}


	public function testDonateNotLoggedIn() {

		$bonets = $this->faker->randomElement($this->donation_sizes);
		$amount = $this->bonets_service->fromBonets($bonets);

		$this->action(
			'POST',
			'RedeemController@donate',
			['id' => $this->donation->id],
			['bonets' => $bonets],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(401);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->bonets, $user->bonets);
	}


	public function testDonateNotEnoughBonets() {

		$this->user->bonets = 1000;
		$this->user->save();

		$this->be($this->user);

		$bonets = $this->faker->randomElement($this->donation_sizes);
		$amount = $this->bonets_service->fromBonets($bonets);

		$this->action(
			'POST',
			'RedeemController@donate',
			['id' => $this->donation->id],
			['bonets' => $bonets],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(422);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->bonets, $user->bonets);

	}

}
