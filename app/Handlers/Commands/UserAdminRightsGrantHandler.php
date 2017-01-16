<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\UserAdminRightsGrant;
use Bonsum\Events\UserAdminRightsGranted;
use Illuminate\Queue\InteractsWithQueue;

class UserAdminRightsGrantHandler {

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
	 * @param  UserAdminRightsGrant  $command
	 * @return void
	 */
	public function handle(UserAdminRightsGrant $command)
	{
		$command->user->admin = true;
		$command->user->save();

		event(new UserAdminRightsGranted($command->user));
	}

}
