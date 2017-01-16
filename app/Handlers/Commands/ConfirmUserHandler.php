<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\ConfirmUser;
use Bonsum\Events\UserConfirmed;
use Illuminate\Queue\InteractsWithQueue;
use Bonsum\Helpers\Mail as MailHelper;
use Bonsum\Commands\CreditBonets;
use Bonsum\Services\Localization;


class ConfirmUserHandler {


	const WELCOME_BONETS = 100;
	const REFERAL_BONETS = 200;

	use \Illuminate\Foundation\Bus\DispatchesCommands;

	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct(Localization $localization)
	{
		$this->localization = $localization;
	}

	/**
	 * Handle the command.
	 *
	 * @param  ConfirmUser  $command
	 * @return void
	 */
	public function handle(ConfirmUser $command)
	{
		if ($command->admin_domain && ends_with($command->user->email, '@' . $command->admin_domain)) {
			$command->user->admin = TRUE;
		}
		$command->user->confirmed = TRUE;
		$command->user->confirmation_code_creation = NULL;
		$command->user->save();

		event(new UserConfirmed($command->user));

		MailHelper::mailUser($command->user, 'new_user_welcome', 'auth.welcome_subject', [
			'welcome_bonets' => self::WELCOME_BONETS,
		]);

		$this->dispatch(new CreditBonets($command->user->id, self::WELCOME_BONETS));

		if ($referer = $command->user->referer) {
			// this user was refered by somebody, let's thank him and give him some bonets

			$this->dispatch(new CreditBonets($referer->id, self::REFERAL_BONETS));

			MailHelper::mailUser($referer, 'referal', 'auth.referal_subject', ['bonets' => self::REFERAL_BONETS]);
		}

	}

}
