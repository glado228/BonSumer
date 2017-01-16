<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Bonsum\User;

class UpdateUserPersonalData extends Command {

	public $user;

	public $data;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, array $data)
	{
		$this->user = $user;
		$this->data = $data;
	}

}
