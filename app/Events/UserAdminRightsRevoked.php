<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class UserAdminRightsRevoked extends UserEvent {

	use SerializesModels;

}
