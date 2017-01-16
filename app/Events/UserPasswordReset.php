<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class UserPasswordReset extends UserEvent {

	use SerializesModels;

	/**
	 * @var string
	 */
	public $reset_link;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $reset_link)
	{
		parent::__construct($user);

		$this->reset_link = $reset_link;
	}

}
