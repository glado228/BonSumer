<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;
use Bonsum\BonetsRedeem;

use Illuminate\Queue\SerializesModels;

class BonetsRedeemed extends UserEvent {

	use SerializesModels;

	/**
	 *
	 * @var Bonsum\BonetsRedeem
	 */
	public $redeem;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, BonetsRedeem $redeem)
	{
		parent::__construct($user);

		$this->redeem = $redeem;
	}

}
