<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\BonetsDonation;
use Bonsum\User;
use Illuminate\Queue\SerializesModels;

class BonetsDonated extends UserEvent {

	use SerializesModels;

	/**
	 *
	 * @var Bonsum\MongoDB\BonetsDonation
	 */
	public $donation;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, BonetsDonation $donation)
	{
		parent::__construct($user);

		$this->donation = $donation;
	}

}
