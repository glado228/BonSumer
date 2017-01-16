<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class UserPersonalDataChanged extends UserEvent {

	use SerializesModels;

	/**
	 * array of data
	 * @var array
	 *
	 */
	public $data;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, array $data)
	{
		parent::__construct($user);

		$this->data = $data;
	}

}
