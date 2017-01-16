<?php namespace Bonsum\Events;

use Bonsum\Events\Event;

use Illuminate\Queue\SerializesModels;

use Bonsum\User;

class UserDeleted extends UserEvent {

	use SerializesModels;


}
