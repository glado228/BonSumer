<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\ResetPassword;
use Bonsum\Events\UserPasswordReset;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Auth\Registrar;
use Bonsum\Helpers\Mail as MailHelper;
use Bonsum\Services\Localization;

class ResetPasswordHandler {

	/**
	 * @var Illuminate\Contracts\Auth\Registrar
	 */
	protected $registrar;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Registrar $registrar, Localization $localization)
	{
		$this->registrar = $registrar;
		$this->localization = $localization;
	}

	/**
	 * Handle the command.
	 *
	 * @param  ResetPassword  $command
	 * @return void
	 */
	public function handle(ResetPassword $command)
	{
		if ($this->registrar->canResetPassword($command->user)) {

			$reset_token = $this->registrar->createPasswordResetToken($command->user);

			$reset_link = $this->localization->getPasswordResetUrl($reset_token, $command->user->preferred_locale);
			$command->user->save();

			MailHelper::mailUser($command->user, 'password', 'auth.password_reset_subject', [
				'reset_link' => $reset_link,
				'invite_url' => FALSE
			]);

			event(new UserPasswordReset($command->user, $reset_link));
		}
	}

}
