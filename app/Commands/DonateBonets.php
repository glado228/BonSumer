<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Bonsum\MongoDB\Donation;

class DonateBonets extends Command {

	public $user_id;

	public $bonets;

	public $receiver;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($user_id, $bonets, Donation $receiver)
	{
		$this->user_id = $user_id;
		$this->bonets = $bonets;
		$this->receiver = $receiver;
	}

}
