<?php


use Bonsum\User;
use Carbon\Carbon;
use Bonsum\Services\Registrar;
use Symfony\Component\DomCrawler\Crawler;


class AccountControllerTest extends TestCase {


	public function setUp() {

		parent::setUp();
		User::unguard();

		$this->registrar = new Registrar();
		$this->app->make('session')->start();

		$this->user_fields = [
			'firstname' => 'Max',
			'lastname' => 'Mustermann',
			'email' => 'nomail@nodomain.com',
			'password' => str_random(10)
		];

		$this->user = $this->registrar->create($this->user_fields);
	}

	public function tearDown() {

		if ($this->user) {
			$this->user->delete();
		}
	}

	public function testUpdatePersonalData() {

		$new_firstname = str_random(20);
		$new_lastname = str_random(30);
		$gender = 'M';

		$this->be($this->user);

		$this->action(
			'POST',
			'AccountController@updateInfo',
			[],
			[
				'firstname' => $new_firstname,
				'lastname' => $new_lastname,
				'gender' => $gender
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseOk();
		$this->user = $this->user->fresh();
		$this->assertEquals($new_firstname, $this->user->firstname);
		$this->assertEquals($new_lastname, $this->user->lastname);
		$this->assertEquals($gender, $this->user->gender);

	}

	public function testUpdatePersonalDataDifferentUser() {

		$new_firstname = str_random(20);
		$new_lastname = str_random(30);
		$gender = 'M';

		$this->be(new User());

		$this->action(
			'POST',
			'AccountController@updateInfoAdmin',
			['user_id' => $this->user->id],
			[
				'firstname' => $new_firstname,
				'lastname' => $new_lastname,
				'gender' => $gender
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->firstname, $user->firstname);
		$this->assertEquals($this->user->lastname, $user->lastname);
		$this->assertEquals($this->user->gender, $user->gender);

	}

	public function testUpdatePersonalDataAdmin() {

		$new_firstname = str_random(20);
		$new_lastname = str_random(30);
		$gender = 'M';

		$this->be(new User(['admin' => true]));

		$this->action(
			'POST',
			'AccountController@updateInfoAdmin',
			['user_id' => $this->user->id],
			[
				'firstname' => $new_firstname,
				'lastname' => $new_lastname,
				'gender' => $gender
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseOk();
		$this->user = $this->user->fresh();
		$this->assertEquals($new_firstname, $this->user->firstname);
		$this->assertEquals($new_lastname, $this->user->lastname);
		$this->assertEquals($gender, $this->user->gender);

	}


	public function testChangePassword() {

		$new_password = str_random(20);

		$this->be($this->user);

		$this->action(
			'POST',
			'AccountController@updatePassword',
			[],
			[
				'new_password' => $new_password,
				'new_password_confirmation' => $new_password,
				'current_password' => $this->user_fields['password']
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseOk();
		$user = $this->user->fresh();
		$this->assertTrue(Hash::check($new_password, $user->password));
	}

}
