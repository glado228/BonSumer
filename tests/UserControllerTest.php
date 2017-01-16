<?php

use Bonsum\User;
use Bonsum\Services\Registrar;


class UserControllerTest extends TestCase {

	use \Illuminate\Foundation\Bus\DispatchesCommands;


	public function setUp() {

		parent::setUp();
		User::unguard();
		$this->app->make('session')->start();
		$this->registrar = new Registrar();

		$this->user_fields = [
			'firstname' => 'Max',
			'lastname' => 'Mustermann',
			'email' => 'nomail@nodomain.com',
			'password' => str_random(10),
			'bonets' => rand(10, 100000)
		];
		$this->user = $this->registrar->create($this->user_fields);

	}

	public function tearDown() {

		$this->user->bonets_credits->each(function($e) {
			$e->delete();
		});
		$this->user->delete();
	}

	public function testDisableUserNoAdmin() {

		$this->be(new User());

		$this->action(
			'POST',
			'Admin\UserController@setDisabled',
			['user' => $this->user->id, 'disabled' => 1],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertEquals(0, $user->disabled);

	}

	public function testActivateUserNoAdmin() {

		$this->user->disabled = true;
		$this->user->save();

		$this->be(new User());

		$this->action(
			'POST',
			'Admin\UserController@setDisabled',
			['user' => $this->user->id, 'disabled' => 0],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertEquals(1, $user->disabled);

	}

	public function testMakeUserAdminNoAdmin() {

		$this->be(new User());

		$this->action(
			'POST',
			'Admin\UserController@setAdmin',
			['user' => $this->user->id, 'admin' => 1],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertEquals(0, $user->admin);
	}

	public function testRevokeAdminNoAdmin() {

		$this->user->admin = true;
		$this->user->save();

		$this->be(new User());

		$this->action(
			'POST',
			'Admin\UserController@setAdmin',
			['user' => $this->user->id, 'admin' => 0],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertEquals(1, $user->admin);
	}

	public function testDeleteUserNoAdmin() {

		$this->be(new User());

		$this->action(
			'DELETE',
			'Admin\UserController@deleteUser',
			['user' => $this->user->id],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertNotNull($user);
	}

	public function testDisableUser() {

		$this->be(new User(['admin' => true]));

		$this->action(
			'POST',
			'Admin\UserController@setDisabled',
			['user' => $this->user->id, 'disabled' => 1],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertEquals(1, $user->disabled);

	}

	public function testActivateUser() {

		$this->user->disabled = true;
		$this->user->save();

		$this->be(new User(['admin' => true]));

		$this->action(
			'POST',
			'Admin\UserController@setDisabled',
			['user' => $this->user->id, 'disabled' => 0],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertEquals(0, $user->disabled);

	}

	public function testMakeUserAdmin() {

		$this->be(new User(['admin' => true]));

		$this->action(
			'POST',
			'Admin\UserController@setAdmin',
			['user' => $this->user->id, 'admin' => 1],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertEquals(1, $user->admin);
	}

	public function testRevokeAdmin() {

		$this->user->admin = true;
		$this->user->save();

		$this->be(new User(['admin' => true]));

		$this->action(
			'POST',
			'Admin\UserController@setAdmin',
			['user' => $this->user->id, 'admin' => 0],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertEquals(0, $user->admin);
	}

	public function testDeleteUser() {

		$this->be(new User(['admin' => true]));

		$this->action(
			'DELETE',
			'Admin\UserController@deleteUser',
			['user' => $this->user->id],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertNull($user);
	}

	public function testCreditBonets() {

		$this->be(new User(['admin' => true]));

		$bonets = rand(10,1000);

		$this->action(
			'POST',
			'Admin\UserController@creditBonets',
			['user' => $this->user->id],
			['bonets' => $bonets],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(200);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->bonets + $bonets, $user->bonets);
	}

	public function testCreditBonetsNoAdmin() {

		$this->be(new User(['admin' => false]));

		$bonets = rand(10,1000);

		$this->action(
			'POST',
			'Admin\UserController@creditBonets',
			['user' => $this->user->id],
			['bonets' => $bonets],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertResponseStatus(403);
		$user = User::find($this->user->id);
		$this->assertEquals($this->user->bonets, $user->bonets);
	}



}
