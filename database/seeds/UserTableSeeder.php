<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Bonsum\User;

class UserTableSeeder extends Seeder {

	const USERNAME = "test@test.com";
	const USERNAME_NO_ADMIN = "noadmin@test.com";
	const PASSWORD = "test";


	public function run() {

		DB::table('users')->delete();

		$user = new User([
			'firstname' => self::USERNAME,
			'lastname' => self::USERNAME,
			'email' => self::USERNAME,
			'password' => bcrypt(self::PASSWORD),
			'admin' => TRUE,
			'confirmed' => TRUE,
			'confirmation_code_creation' => Carbon::now(),
			'reset_token_creation' => Carbon::now(),
			'preferred_locale' => App::getLocale()
		]);

		$user->save();

		$user = new User([
			'firstname' => self::USERNAME_NO_ADMIN,
			'lastname' => self::USERNAME_NO_ADMIN,
			'email' => self::USERNAME_NO_ADMIN,
			'password' => bcrypt(self::PASSWORD),
			'admin' => FALSE,
			'confirmed' => TRUE,
			'confirmation_code_creation' => Carbon::now(),
			'reset_token_creation' => Carbon::now(),
			'preferred_locale' => App::getLocale()
		]);

		$user->save();

	}
}
