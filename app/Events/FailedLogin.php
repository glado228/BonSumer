<?php namespace Bonsum\Events;

use Bonsum\Events\Event;

use Illuminate\Queue\SerializesModels;

class FailedLogin extends Event {

	use SerializesModels;

	/**
	 * The email that was used to login
	 * @var string
	 */
	public $email;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($email)
	{
		parent::__construct();
		$this->email = $email;
	}
}
