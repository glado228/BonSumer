<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\DisableUser;
use Bonsum\Events\UserDisabled;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;

class DisableUserHandler {

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
	 * @param  DisableUser  $command
	 * @return void
	 */
	public function handle(DisableUser $command)
	{
		$command->user->disabled = TRUE;
		$command->user->disabled_at = new Carbon;
		$command->user->save();
		event(new UserDisabled($command->user));
	}

}
