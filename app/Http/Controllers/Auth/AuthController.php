<?php namespace Bonsum\Http\Controllers\Auth;

use Bonsum\Http\Controllers\Controller;
use Bonsum\Events\FailedLogin;
use Bonsum\Events\UserPasswordChanged;
use Bonsum\Events\OldPasswordImported;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Bonsum\Services\FrontEnd;
use Bonsum\Commands\ConfirmUser;
use Bonsum\Commands\NewUser;
use Bonsum\Commands\ResetPassword;
use Bonsum\Commands\ChangePassword;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Contracts\Encryption\DecryptException;
use Bonsum\User;
use Carbon\Carbon;
use Session;
use Mail;
use App;
use Crypt;


class AuthController extends Controller {



	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogin()
	{
		return view('auth.login');
	}

	/**
	 * Show the application registration form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getSignup(FrontEnd $fe, $referer_id = NULL)
	{
		$referer_name = NULL;

		if ($referer_id) {
			$user = NULL;
			try {
				$user = User::find(Crypt::decrypt($referer_id));
			} catch (DecryptException $de) {}

			if ($user) {
				$referer_name = $user->fullname;
				$referer_id = $user->id;
			} else {
				$referer_id = NULL;
			}
		}

		$fe->addVars([
			'signupUrl' => action('Auth\AuthController@postSignup'),
			'SIGNING_UP_MESSAGE' => trans('auth.signing_up')
		]);

		$fe->addPiwikGoal(4);

		if ($this->auth->guest()) {
			return view('auth.signup')->with([
				'referer_id' => $referer_id,
				'referer_name' => $referer_name
			]);
		}


		return view('auth.signup_admin');
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function postLogout(Request $request)
	{
		$this->auth->logout();

		if ($request->ajax()) {
			return action('HomeController@index');
		} else {
			return redirect()->action('HomeController@index');
		}
	}


	/**
	 * Parse a post request to create new user
	 * @param  Registrar $registrar the Registrar service
	 * @param  Request   $request   the Request
	 * @return Response
	 */
	public function postSignup(Request $request) {

		$this->validate($request,
			array_merge(
				$this->registrar->validator([])->getRules(),
				[
				 'terms_and_conditions' => 'accepted'
				]
			)
		);

		if ($request->get('validate') && $request->ajax()) {
			Session::reflash();
			return;
		}

		$is_admin = $this->auth->user() && $this->auth->user()->admin;

		$this->dispatch(new NewUser(
			$request->input('firstname'),
			$request->input('lastname'),
			$request->input('gender'),
			$request->input('email'),
			$request->input('password'),
			(!$is_admin ? true : $request->input('send_activation_email')),
			(!$is_admin ? false : $request->input('admin')),
			App::getLocale(),
			$request->input('referer_id')
		));

		Session::flash('notification_title', trans('auth.signup_confirm_title'));
		Session::flash('notification_text', trans('auth.signup_confirm_message', ['email' => $request->get('email')]));

		if ($request->ajax()) {
			return action('Auth\AuthController@showNotification');
		} else {
			return redirect()->action('Auth\AuthController@showNotification');
		}
	}

	/**
	 * Show a notification to the user
	 *
	 * title and message must be in the session as "notification_title" and "notification_text"
	 *
	 */
	public function showNotification() {

		if (!Session::has('notification_title')) {
			return redirect()->action('HomeController@index');
		}

		return view('auth.notification')->with([
			'notification_title' => Session::get('notification_title'),
			'notification_text' => Session::get('notification_text')
		]);
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email', 'password' => 'required',
		]);

		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt(array_merge($credentials, ['confirmed' => TRUE, 'disabled' => FALSE]), $request->has('remember')))
		{
			return redirect()->intended(action('HomeController@index'));
		}
		else {

			/*
				The user has failed login. In order to recover passwords from the previous site implementation,
				we verify the submitted password against the stored old one.
				If they match, this is a user who's logging in to the new site for the first time.
				We therefore rehash her password and log her in
			 */

			$user = User::where('email', '=', $request->input('email'))->first();
			if ($user && $user->confirmed && !$user->disabled && $user->old_password) {

				$wp_hasher = new \Bonsum\Helpers\PasswordHash( 8, true );

				if ($wp_hasher->CheckPassword($request->input('password'), $user->old_password)) {

					$this->registrar->setPassword($user, $request->input('password'));
					$user->old_password = NULL;
					$user->save();

					if ($this->auth->attempt(array_merge($credentials, ['confirmed' => TRUE, 'disabled' => FALSE]), $request->has('remember'))) {

						event(new OldPasswordImported($user));
						return redirect()->intended(action('HomeController@index'));
					}
				}
			}

			event(new FailedLogin($request->get('email')));
		}

		return redirect()->action('Auth\AuthController@getLogin')
					->withInput($request->only('email'))
					->withErrors([
						'failed_login' => 'login failed'
					]);
	}

	/**
	 * Processs activation link
	 * @param  Request $request
	 * @return Response
	 */
	public function getActivate($confirmation_code) {

		$user = $this->auth->getProvider()->retrieveByCredentials([
			'confirmation_code' => $confirmation_code,
			'confirmed' => FALSE
		]);

		if (!$user) {
			return view('auth.notification')->with([
				'notification_title' => trans('auth.activation_failed_title'),
				'notification_text' => trans('auth.activation_failed_message')
			]);
		}

		$this->dispatch(new ConfirmUser($user, config('auth.admin_domain')));

		return view('auth.login')->with([
			'login_header' => trans('auth.activation_success_title'),
			'email' => $user->email
		]);
	}

	/**
	 * Display the form to reuqest a password reset
	 * @return View
	 */
	public function getPasswordReset() {

		return view('auth.password_reset');
	}

	/**
	 * Processes a password reset request
	 * @param  $request Request
	 * @return View
	 */
	public function postPasswordReset(Request $request) {

		$this->validate($request, ['email' => 'required|email|exists:users,email,confirmed,1']);

		$user = $this->auth->getProvider()->retrieveByCredentials([
			'email' => $request->get('email'),
			'confirmed' => TRUE
		]);

		$this->dispatch(new ResetPassword($user));

		return view('auth.notification')->with([
			'notification_title' => trans('auth.reset_email_confirm_title'),
			'notification_text' => trans('auth.reset_email_confirm_message', ['email' => $user->email])
		]);
	}

	/**
	 * Displays the password upadte form
	 * @param  string $reset_token the reset token
	 * @return View
	 */
	public function getNewPassword($reset_token) {

		$user = $this->auth->getProvider()->retrieveByCredentials([
			'reset_token' => $reset_token,
			'confirmed' => TRUE
		]);

		if (!$user) {
			return view('auth.notification')->with([
				'notification_title' => trans('auth.new_password_failed_title'),
				'notification_text' => trans('auth.new_password_failed_message', ['link' => action('Auth\AuthController@getPasswordReset')])
			]);
		}

		return view('auth.new_password')->with([
			'reset_token' => $reset_token,
			'email' => $user->email
		]);
	}

	/**
	 * processes the password update
	 * @param  Request $request
	 * @return Redirect to home page or error View
	 */
	public function postNewPassword(Request $request) {

		$this->validate($request, [
			'password' => 'required|confirmed|min:6',
			'email' => 'required',
			'reset_token' => 'required'
			// 'email' and 'reset_token' are hidden fields
		]);

		$user = $this->auth->getProvider()->retrieveByCredentials([
			'reset_token' => $request->get('reset_token'),
			'confirmed' => TRUE
		]);

		// canResetPassword tells us if this token is no longer valid
		if (!$user || $user->email != $request->get('email') || $this->registrar->canResetPassword($user)) {
			return view('auth.notification')->with([
				'notification_title' => trans('auth.new_password_failed_title'),
				'notification_text' => trans('auth.new_password_failed_message', ['link' => action('Auth\AuthController@getPasswordReset')])
			]);
		}

		$this->dispatch(new ChangePassword($user, $request->password));

		return redirect()->action('HomeController@index');
	}

	/**
	 * Get a json description of the current user
	 * @return [type] [description]
	 */
	public function getUser() {

		return response()->json($this->auth->user());
	}


	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'postLogout', 'getUser']);
	}

}
