<?php namespace Bonsum\Services;

use Illuminate\Contracts\Auth\Guard;
use Laravel\Socialite\Two\User;
use Bonsum\Commands\ConfirmUser;
use Validator;
use Auth;
use App;
use Exception;
use Bonsum\Events\UserCreated;
use Illuminate\Contracts\Auth\Registrar;

class FIWareUser {

	use \Illuminate\Foundation\Bus\DispatchesCommands;


	protected $guard = null;
	protected $registrar = null;


	public function __construct(Guard $guard, Registrar $registrar) {

		$this->guard = $guard;
		$this->registrar = $registrar;
	}

	/**
	 * login a fiware user, creating it in our user database table if he does not exist yet
	 * @param  User   $fiware_user fiware user
	 * @return [type]              [description]
	 */
	public function createIfNewAndLogin(User $fiware_user) {
		// look up user
		$user = $this->guard->getProvider()->retrieveByCredentials([
			'email' => $fiware_user->email
		]);

		list($firstname, $lastname) = self::splitName($fiware_user->name);

		if (!$user) {

			// creaet a normal Bonsum user here using NeuUser with no activation email

			$new_user_fields = [
				'firstname' => $firstname,
				'lastname' => $lastname,
				'email' => $fiware_user->email,
				'password' => str_random(60) // randomly generated password, never to be used
			];

			$validator = $this->registrar->validatorSocial($new_user_fields);
			if ($validator->fails()) {
				throw new Exception('FIWare user did not pass validation. '. var_export($validator->messages(), true));
			}

			$user = $this->registrar->create(
				$new_user_fields,
				true,
				false
			);

			event(new UserCreated($user));
		}

		if (!$user->confirmed) {
			$this->dispatch(new ConfirmUser($user, null, true));
		}


		// login user
		$this->guard->login($user);
	}

	/**
	 * split a Socialite user name into first and last names
	 * @param  string $name full name
	 * @return array       (firtstanme, lastname)
	 */
	static protected function splitName($name) {

		$pieces = preg_split('/\s+/', trim($name), 2);

		if (count($pieces) < 2) {
			$pieces[] = '';
		}

		return $pieces;
	}

}
