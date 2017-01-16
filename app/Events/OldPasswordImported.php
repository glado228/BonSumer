<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class OldPasswordImported extends UserEvent {

	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		parent::__construct($user);
	}

}
