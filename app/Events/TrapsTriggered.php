<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Illuminate\Http\Request;

use Illuminate\Queue\SerializesModels;

class TrapsTriggered extends Event {

	use SerializesModels;

	/**
	 * Request associated with this event
	 * @var Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Create a new event instance.
	 * This event is fired when a request triggers the form traps in Bonsum\Http\Middleware\Traps
	 *
	 * @return void
	 */
	public function __construct(Request $request)
	{
		parent::__construct();

		$this->request = $request;
	}

}
