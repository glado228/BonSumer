<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\SendConfirmationReminder;
use Mail;
use Bonsum\Helpers\Mail as MailHelper;
use Illuminate\Queue\InteractsWithQueue;
use Bonsum\Events\UserConfirmationReminderSent;
use Bonsum\Services\Registrar;
use Bonsum\Services\Localization;

class SendConfirmationReminderHandler {


	/**
	 * Registrar service
	 * @var [type]
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
	 * @param  SendConfirmationReminder  $command
	 * @return void
	 */
	public function handle(SendConfirmationReminder $command)
	{
		$user = $command->user;

		if (!$user->confirmed && $user->confirmation_code && $this->registrar->canSendConfirmation($user))  {

			$activation_link = $this->localization->getActivationUrl($user->confirmation_code, $user->preferred_locale);

			MailHelper::mailUser($user, 'activation_reminder', 'auth.activation_reminder_subject', [
				'activation_link' => $activation_link,
				'invite_url' => FALSE
			]);

			$user->confirmation_reminder_sent = true;
			$user->save();

			event(new UserConfirmationReminderSent($user, $activation_link));
		}
	}

}
