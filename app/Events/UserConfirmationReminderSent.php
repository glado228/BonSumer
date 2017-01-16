<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class UserConfirmationReminderSent extends UserEvent {

	use SerializesModels;

	public $activation_link;


	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $activation_link)
	{
		parent::__construct($user);

		$this->activation_link = $activation_link;

		//
	}

}
