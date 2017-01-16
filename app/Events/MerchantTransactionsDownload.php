<?php namespace Bonsum\Events;

use Bonsum\Events\Event;
use Carbon\Carbon;

use Illuminate\Queue\SerializesModels;

class MerchantTransactionsDownload extends Event {

	use SerializesModels;

	protected $time;

	protected $count;

	protected $errors;

	protected $from_date;

	protected $to_date;

	protected $save;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($time, $count, $errors, Carbon $from_date, Carbon $to_date, $save)
	{
		parent::__construct();

		$this->time = $time;
		$this->count = $count;
		$this->errors = $errors;
		$this->from_date = new Carbon($from_date);
		$this->to_date = new Carbon($to_date);
		$this->save = $save;
	}


}
