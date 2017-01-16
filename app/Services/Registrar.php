<?php namespace Bonsum\Services;

use Bonsum\User;
use Carbon\Carbon;
use Validator;
use App;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract {


	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'firstname' => 'required|max:200',
			'lastname' => 'max:200',
			'gender' => 'in:M,F',
			'email' => 'required|email|max:200|unique:users,email,NULL,id,confirmed,1|confirmed',
			'password' => 'required|min:6|confirmed'
		]);
	}

	/**
	 * Validator for OAuth2 users
	 * @param  array  $data user data from OAuth2 provdier
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validatorSocial(array $data) {

		return Validator::make($data, [
			'firstname' => 'required|max:200',
			'lastname' => 'max:200',
			'gender' => 'in:M,F',
			'email' => 'required|email|max:200|unique:users,email,NULL,id,confirmed,1'
		]);
	}

	/**
	 * updat a User password
	 * @param User   $user     User model
	 * @param string $password  password
	 * @return User the user model
	 */
	public function setPassword(User $user, $password) {
		$user->password = bcrypt($password);
		return $user;
	}

	/**
	 * True if the user cna reset the passsword, false, if we already had a token sent in the last hour
	 * @param  User   $user [description]
	 * @return [type]       [description]
	 */
	public function canResetPassword(User $user) {

		return (!$user->reset_token || $user->reset_token_creation->diffInHours() >= 1);
	}

	/**
	 * True if we can send a confirmation reminder, false if we sent one in the last houer
	 * @param  User   $user [description]
	 * @return [type]       [description]
	 */
	public function canSendConfirmation(User $user) {

		return (!$user->confirmed && (!$user->confirmation_code_creation || $user->confirmation_code_creation->diffInHours() >= 1));
	}


	/**
	 * create a new password reset token
	 * @param  User   $user the user model
	 * @return string the newly generated reset tokone or FALSE if no token was generated
	 */
	public function createPasswordResetToken(User $user) {

		$user->reset_token = str_random(60);
		$user->reset_token_creation = new Carbon();
		return $user->reset_token;
	}

	/**
	 * Generate a new confirmation code
	 * @param  User   $user the user model
	 * @return string the confirmation code or FALSE if no new confirmation code was generated
	 */
	public function createConfirmationCode(User $user) {

		$user->confirmation_code = str_random(60);
		$user->confirmation_code_creation = new Carbon();
		return $user->confirmation_code;
	}

	/**
	 * Update fillable user fields and password. Does not save the model.
	 * @param  User   $user  user model
	 * @param  array $data  user data
	 * @return User        user model
	 */
	public function update(User $user, array $data) {

		$user->fill([
			'email' => $data['email'],
			'firstname' => $data['firstname'],
			'lastname' => (isset($data['lastname']) ? $data['lastname'] : ''),
			'gender' => (isset($data['gender']) ? $data['gender'] : NULL),
			'preferred_locale' => (isset($data['preferred_locale']) ? $data['preferred_locale'] : App::getLocale()),
			'referer_id' => (isset($data['referer_id']) ? $data['referer_id'] : NULL)
		]);
		if (isset($data['admin'])) {
			$user->admin = $data['admin'];
		}
		if (isset($data['password'])) {
			$this->setPassword($user, $data['password']);
		}
		return $user;
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @param  bool  $save whether to save the new user
	 * @return User
	 */
	public function create(array $data, $save = TRUE, $confirmed = FALSE)
	{
		$user = new User();
		$this->update($user, $data);
		$user->confirmed = $confirmed;
		if ($save) {
			$user->save();
		}
		return $user;
	}


	public function __construct() {
	}

}
