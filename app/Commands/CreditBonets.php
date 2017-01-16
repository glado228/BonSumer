<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Bonsum\User;

class CreditBonets extends Command {

	const DEFAULT_MAX_CREDITABLE = 100000;

	/**
	 * Id of the user that will receive the bonets
	 * @var int
	 */
	public $user_id;

	/**
	 * bonets to be credited to the user
	 * @var int
	 */
	public $bonets;

	/**
	 * optional personalized message
	 * @var string
	 */
	public $message;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($user_id, $bonets, $message = null)
	{
		$this->user_id = $user_id;
		$this->bonets = $bonets;
		$this->message = $message;
	}

}
