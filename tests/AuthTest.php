<?php

use Bonsum\User;
use Carbon\Carbon;
use Bonsum\Services\Registrar;
use Symfony\Component\DomCrawler\Crawler;


class AuthTest extends TestCase {


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

		$this->referer_fields = [
			'firstname' => 'Referer',
			'lastname' => 'Mustermann',
			'email' => 'referer@nodomain.com',
			'password' => str_random(10)
		];

		$this->referer = $this->registrar->create($this->referer_fields);
		$this->user = $this->registrar->create($this->user_fields);
	}

	public function tearDown() {

		foreach ([$this->referer, $this->user] as $user) {
			if ($user) {
				$user->bonets_credits->each(function($credit) {
					$credit->delete();
				});
				$user->delete();
			}
		}
	}

	public function testLogin() {

		$this->user->confirmed = TRUE;
		$this->user->save();

		$this->action(
			'POST',
			'Auth\AuthController@postLogin',
			[],
			[
				'email' => $this->user_fields['email'],
				'password' => $this->user_fields['password']
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertRedirectedToAction('HomeController@index');
		$this->assertTrue(app('auth.driver')->check());
		$this->assertEquals($this->user->email, app('auth.driver')->user()->email);

	}

	public function testLoginUnconfirmed() {

		$this->action(
			'POST',
			'Auth\AuthController@postLogin',
			[],
			[
				'email' => $this->user_fields['email'],
				'password' => $this->user_fields['password']
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertRedirectedToAction('Auth\AuthController@getLogin');
		$this->assertSessionHasErrors(['failed_login']);
		$this->assertTrue(app('auth.driver')->guest());
	}

	public function testLoginWrongPassword() {

		$this->user->confirmed = TRUE;
		$this->user->save();

		$this->action(
			'POST',
			'Auth\AuthController@postLogin',
			[],
			[
				'email' => $this->user->email,
				'password' => 'password1'
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertRedirectedToAction('Auth\AuthController@getLogin');
		$this->assertSessionHasErrors(['failed_login']);
		$this->assertTrue(app('auth.driver')->guest());
	}

	public function testLogout() {

		$this->be($this->user);

		$this->action(
			'POST',
			'Auth\AuthController@postLogout',
			[],
			[],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertRedirectedToAction('HomeController@index');
		$this->assertTrue(app('auth.driver')->guest());
	}


	public function testSignup() {

		$this->user->delete();

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'],
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertRedirectedToAction('Auth\AuthController@showNotification');
		$this->assertNotNull($this->user);
		$this->assertEquals(0, $this->user->confirmed);
		$this->assertNotNull($this->user->confirmation_code);
		$this->assertEquals(0, $this->user->bonets);
		$this->assertEquals(0, $this->user->admin);
	}

	public function testSecondSignupWithinOneHour() {

		$this->registrar->createConfirmationCode($this->user);
		$this->user->save();
		$first_code = $this->user->confirmation_code;

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'],
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertRedirectedToAction('Auth\AuthController@showNotification');
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertEquals(0, $this->user->confirmed);
		$this->assertEquals($first_code, $this->user->confirmation_code);
	}

	public function testSecondSignupAfterOneHour() {

		$this->registrar->createConfirmationCode($this->user);
		$this->user->confirmation_code_creation = Carbon::now()->subMinutes(61);
		$this->user->save();
		$first_code = $this->user->confirmation_code;

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'],
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->assertRedirectedToAction('Auth\AuthController@showNotification');
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertEquals(0, $this->user->confirmed);
		$this->assertNotNull($this->user->confirmation_code);
		$this->assertNotEquals($first_code, $this->user->confirmation_code);
	}

	public function testSignupNonMatchingPasswords() {

		$this->user->delete();

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'] . '1',
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getSignup')]
		);

		$this->assertRedirectedToAction('Auth\AuthController@getSignup');
		$this->assertSessionHasErrors(['password']);
		$this->assertEmpty(User::where('email', '=', $this->user_fields['email'])->get());
	}

	public function testActivation() {

		$confirmation_code = $this->registrar->createConfirmationCode($this->user);
		$this->user->save();

		$this->action(
			'GET',
			'Auth\AuthController@getActivate',
			[
				'confirmation_code' => $confirmation_code
			]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertEquals(1, $this->user->confirmed);
	}

	public function testActivationWrongCode() {

		$confirmation_code = $this->registrar->createConfirmationCode($this->user);
		$this->user->save();

		$this->action(
			'GET',
			'Auth\AuthController@getActivate',
			[
				'confirmation_code' => $confirmation_code . '1'
			]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertEquals(0, $this->user->confirmed);
	}

	public function testActivationWithReferer() {

		$confirmation_code = $this->registrar->createConfirmationCode($this->user);
		$this->user->referer_id = $this->referer->id;
		$this->user->save();

		$this->action(
			'GET',
			'Auth\AuthController@getActivate',
			[
				'confirmation_code' => $confirmation_code
			]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertEquals(1, $this->user->confirmed);

		$this->referer = User::where('email', '=', $this->referer_fields['email'])->first();
		$this->assertEquals(\Bonsum\Handlers\Commands\ConfirmUserHandler::REFERAL_BONETS, $this->referer->bonets);
	}

	public function testPasswordResetRequest() {

		$this->user->confirmed = TRUE;
		$this->user->save();

		$this->action(
			'POST',
			'Auth\AuthController@postPasswordReset',
			[],
			[
				'email' => $this->user->email
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getPasswordReset')]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertNotNull($this->user->reset_token);
	}

	public function testPasswordResetRequestUnconfirmed() {

		$this->action(
			'POST',
			'Auth\AuthController@postPasswordReset',
			[],
			[
				'email' => $this->user->email
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getPasswordReset')]
		);

		$this->assertRedirectedToAction('Auth\AuthController@getPasswordReset');
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertNull($this->user->reset_token);
	}

	public function testPasswordResetRequestWithinOneHour() {

		$this->user->confirmed = TRUE;
		$this->registrar->createPasswordResetToken($this->user);
		$this->user->save();

		$first_token = $this->user->reset_token;

		$this->action(
			'POST',
			'Auth\AuthController@postPasswordReset',
			[],
			[
				'email' => $this->user->email
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getPasswordReset')]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertEquals($first_token, $this->user->reset_token);
	}

	public function testPasswordResetRequestAfterOneHour() {

		$this->user->confirmed = TRUE;
		$this->registrar->createPasswordResetToken($this->user);
		$this->user->reset_token_creation = Carbon::now()->subMinutes(61);
		$this->user->save();

		$first_token = $this->user->reset_token;

		$this->action(
			'POST',
			'Auth\AuthController@postPasswordReset',
			[],
			[
				'email' => $this->user->email
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getPasswordReset')]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertNotNull($this->user);
		$this->assertNotEquals($first_token, $this->user->reset_token);
	}

	public function testNewPasswordForm() {

		$reset_token = $this->registrar->createPasswordResetToken($this->user);
		$this->user->confirmed = TRUE;
		$this->user->save();

		$response = $this->action(
			'GET',
			'Auth\AuthController@getNewPassword',
			[
				'reset_token' => $reset_token
			]
		);

		$this->assertResponseOk();
		$this->assertViewHas('email', $this->user->email);
		$this->assertViewHas('reset_token', $reset_token);
	}

	public function testPasswordUpdate() {

		$reset_token = $this->registrar->createPasswordResetToken($this->user);
		$this->user->confirmed = TRUE;
		$this->user->save();

		$new_password = str_random(10);

		$response = $this->action(
			'POST',
			'Auth\AuthController@postNewPassword',
			[],
			[
				'reset_token' => $reset_token,
				'email' => $this->user->email,
				'password' => $new_password,
				'password_confirmation' => $new_password
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getNewPassword')]
		);

		$this->assertRedirectedToAction('HomeController@index');
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertTrue(app('auth.driver')->check());
		$this->assertEquals($this->user->email, app('auth.driver')->user()->email);
		$this->assertNull($this->user->reset_token);
		$this->assertTrue(Hash::check($new_password, $this->user->password));
	}

	public function testPasswordUpdateWrongToken() {

		$reset_token = $this->registrar->createPasswordResetToken($this->user);
		$this->user->confirmed = TRUE;
		$this->user->save();

		$new_password = str_random(10);
		$old_password = $this->user->password;

		$response = $this->action(
			'POST',
			'Auth\AuthController@postNewPassword',
			[],
			[
				'reset_token' => $reset_token . 'a',
				'email' => $this->user->email,
				'password' => $new_password,
				'password_confirmation' => $new_password
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getNewPassword')]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertTrue(app('auth.driver')->guest());
		$this->assertNotNull($this->user->reset_token);
		$this->assertEquals($old_password, $this->user->password);
	}

	public function testPasswordUpdateAfterOneHour() {

		$reset_token = $this->registrar->createPasswordResetToken($this->user);
		$this->user->reset_token_creation = Carbon::now()->subMinutes(61);
		$this->user->confirmed = TRUE;
		$this->user->save();

		$new_password = str_random(10);
		$old_password = $this->user->password;

		$response = $this->action(
			'POST',
			'Auth\AuthController@postNewPassword',
			[],
			[
				'reset_token' => $reset_token,
				'email' => $this->user->email,
				'password' => $new_password,
				'password_confirmation' => $new_password
			],
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token(), 'HTTP_REFERER' => action('Auth\AuthController@getNewPassword')]
		);

		$this->assertResponseOk();
		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertTrue(app('auth.driver')->guest());
		$this->assertNotNull($this->user->reset_token);
		$this->assertEquals($old_password, $this->user->password);
	}


	public function testSignupAdmin() {

		$this->user->delete();

		$admin = new User(['admin' => true]);
		$this->be($admin);

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'],
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE,
					'send_activation_email' => FALSE,
					'admin' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertRedirectedToAction('Auth\AuthController@showNotification');
		$this->assertNotNull($this->user);
		$this->assertEquals(1, $this->user->confirmed);
		$this->assertNull($this->user->confirmation_code);
		$this->assertEquals(100, $this->user->bonets);
		$this->assertEquals(1, $this->user->admin);

	}


	public function testSignupNoAdminWithDangerousFields() {

		$this->user->delete();

		$noadmin = new User();
		$this->be($noadmin);

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'],
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE,
					'send_activation_email' => FALSE,
					'admin' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertRedirectedToAction('HomeController@index');
		$this->assertNull($this->user);
	}

	public function testSignupGuestWithDangerouslFields() {

		$this->user->delete();

		$this->action(
			'POST',
			'Auth\AuthController@postSignup',
			[],
			array_merge($this->user_fields,
				[
					'password_confirmation' => $this->user_fields['password'],
					'email_confirmation' => $this->user_fields['email'],
					'terms_and_conditions' => TRUE,
					'send_activation_email' => FALSE,
					'admin' => TRUE
				]
			),
			[],
			[],
			['HTTP_X-CSRF-TOKEN' => csrf_token()]
		);

		$this->user = User::where('email', '=', $this->user_fields['email'])->first();
		$this->assertRedirectedToAction('Auth\AuthController@showNotification');
		$this->assertNotNull($this->user);
		$this->assertEquals(0, $this->user->confirmed);
		$this->assertNotNull($this->user->confirmation_code);
		$this->assertEquals(0, $this->user->bonets);
		$this->assertEquals(0, $this->user->admin);
	}
}
