<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\ActivateUser;
use Bonsum\Events\UserActivated;
use Mail;
use Illuminate\Queue\InteractsWithQueue;
use Bonsum\Commands;

class ActivateUserHandler {



	/**
	 * Create the command handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the command.
	 *
	 * @param  ActivateUser  $command
	 * @return void
	 */
	public function handle(ActivateUser $command)
	{
		$command->user->disabled = FALSE;
		$command->user->disabled_at = NULL;
		$command->user->save();

		event(new UserActivated($command->user));
	}

}
