<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;
use Illuminate\Queue\SerializesModels;

class UserActivated extends UserEvent {

	use SerializesModels;

}
