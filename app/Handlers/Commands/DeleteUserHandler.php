<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\DeleteUser;
use Bonsum\Events\UserDeleted;
use Illuminate\Queue\InteractsWithQueue;

class DeleteUserHandler {

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
	 * @param  DeleteUser  $command
	 * @return void
	 */
	public function handle(DeleteUser $command)
	{
		$command->user->delete();
		event(new UserDeleted($command->user));
	}

}
