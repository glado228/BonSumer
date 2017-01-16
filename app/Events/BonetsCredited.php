<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\BonetsCredit;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class BonetsCredited extends UserEvent {

	use SerializesModels;

	/**
	 * BonetsCredit
	 * @var [type]
	 */
	public $bonetsCredit;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, BonetsCredit $credit)
	{
		parent::__construct($user);

		$this->bonetsCredit = $credit;
	}

}
