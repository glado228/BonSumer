<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Bonsum\User;

class UserAdminRightsGrant extends Command {

	public $user;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

}
