<?php namespace Bonsum\Events;

use Bonsum\Events\Event;

use Illuminate\Queue\SerializesModels;

class BonetsComputed extends Event {

	use SerializesModels;

	public $time;

	public $total_bonets;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($time, $total_bonets)
	{
		$this->time = $time;
		$this->total_bonets = $total_bonets;
	}

}
