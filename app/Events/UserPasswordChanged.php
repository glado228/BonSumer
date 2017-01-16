<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class UserPasswordChanged extends UserEvent {

	use SerializesModels;



	public function __construct(User $user) {

		parent::__construct($user);
	}

}
