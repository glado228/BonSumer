<?php namespace Bonsum\Handlers\Commands;

use Bonsum\Commands\UpdateUserPersonalData;
use Bonsum\Events\UserPersonalDataChanged;
use Illuminate\Queue\InteractsWithQueue;
use Bonsum\User;

class UpdateUserPersonalDataHandler {

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
	 * @param  UpdateUserPersonalData  $command
	 * @return void
	 */
	public function handle(UpdateUserPersonalData $command)
	{
		$command->user->fill($command->data);
		$command->user->save();

		event(new UserPersonalDataChanged($command->user, $command->data));
	}

}
