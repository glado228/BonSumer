<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;
use Illuminate\Queue\SerializesModels;

class UserDisabled extends UserEvent {

	use SerializesModels;

}
