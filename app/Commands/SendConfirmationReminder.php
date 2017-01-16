<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;

class SendConfirmationReminder extends Command {


	public $user;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

}
