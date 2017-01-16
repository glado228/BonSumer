<?php namespace Bonsum\Commands;

use Bonsum\User;
use Bonsum\Commands\Command;

class ChangePassword extends Command {

	public $user;

	public $new_password;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $new_password)
	{
		$this->user = $user;
		$this->new_password = $new_password;
	}

}
