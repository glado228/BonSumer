<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Bonsum\User;

use Illuminate\Queue\SerializesModels;

class BonetsUpdateNotCommitted extends UserEvent {

	use SerializesModels;

	/**
	 * Bonets the user had before starting the update operation
	 * @var [type]
	 */
	public $bonets_update_start;

	/**
	 * Bonets computed by the update operation but that were not written
	 * @var [type]
	 */
	public $bonets_computed;

	/**
	 * Bonets the user had when the update operation attempted to write the computed bonets
	 * @var [type]
	 */
	public $bonets_update_end;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $bonets_update_start, $bonets_computed, $bonets_update_end)
	{
		parent::__construct($user);

		$this->bonets_update_start = $bonets_update_start;
		$this->bonets_computed = $bonets_computed;
		$this->bonets_update_end = $bonets_update_end;
	}

}
