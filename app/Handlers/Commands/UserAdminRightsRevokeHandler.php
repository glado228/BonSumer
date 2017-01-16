<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\UserAdminRightsRevoke;
use Bonsum\Events\UserAdminRightsRevoked;

use Illuminate\Queue\InteractsWithQueue;

class UserAdminRightsRevokeHandler {

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
	 * @param  UserAdminRightsRevoke  $command
	 * @return void
	 */
	public function handle(UserAdminRightsRevoke $command)
	{
		$command->user->admin = false;
		$command->user->save();

		event(new UserAdminRightsRevoked($command->user));
	}

}
