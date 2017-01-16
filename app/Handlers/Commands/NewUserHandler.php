<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\NewUser;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Contracts\Auth\Guard;
use Bonsum\Events\UserCreated;
use Bonsum\Events\UserConfirmationCodeSent;
use App;
use Bonsum\Helpers\Mail as MailHelper;
use Bonsum\Commands\ConfirmUser;
use Bonsum\Services\Localization;

use Illuminate\Queue\InteractsWithQueue;

class NewUserHandler {

	use \Illuminate\Foundation\Bus\DispatchesCommands;

	/**
	 *
	 * @var Bonsum\Services\Registrar
	 */
	protected $registrar;

	/**
	 * @var Illuminate\Contracts\Auth\Guard
	 */
	protected $auth;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Registrar $registrar, Guard $auth, Localization $localization)
	{
		$this->registrar = $registrar;
		$this->auth = $auth;
		$this->localization = $localization;
	}

	/**
	 * Handle the command.
	 *
	 * @param    $command
	 * @return void
	 */
	public function handle(NewUser $command)
	{
		// At this point, $command->email should either not exist or belong to an unconfirmed account
		// In no cases should it belong to a confirmed account

		$user = $this->auth->getProvider()->retrieveByCredentials([
			'email' => $command->email,
			'confirmed' => FALSE
		]);

		if (!$user) {
			$user = $this->registrar->create(
				get_object_vars($command),
				FALSE
			);
			event(new UserCreated($user));
		} else {
			$user = $this->registrar->update($user, get_object_vars($command));
		}
		$user->save();

		if ($command->send_activation_email) {

			// The following check is to limit generation and sending of
			// new confirmation codes for one email address to 1 code per hour
			if ($this->registrar->canSendConfirmation($user)) {
				$confirmation_code = $this->registrar->createConfirmationCode($user);

				$activation_link = $this->localization->getActivationUrl($confirmation_code, $user->preferred_locale);

				$this->registrar->setPassword($user, $command->password);
				$user->save();

				MailHelper::mailUser($user, 'activation', 'auth.activation_subject', [
					'activation_link' => $activation_link,
					'invite_url' => FALSE
				]);
				event(new UserConfirmationCodeSent($user, $activation_link));
			}

		} else {
			// this is an user created by an admin for which no confirmation email was requested
			// we activate the user right away
			$this->dispatch(new ConfirmUser($user, config('auth.admin_domain')));
		}

	}

}
