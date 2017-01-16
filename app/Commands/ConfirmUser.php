<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Bonsum\User;

class ConfirmUser extends Command {

	/**
	 *
	 * @var Bonsum\User
	 */
	public $user;

	/**
	 * 	whether to make the user an admin if email is in certain domain
	 * @var string|null
	 */
	public $admin_domain;

	/**
	 *
	 *
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $admin_domain = NULL)
	{
		$this->user = $user;
		$this->admin_domain = $admin_domain;
	}

}
