<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Carbon\Carbon;

use Illuminate\Contracts\Bus\SelfHandling;

class FetchTransactions extends Command {

	/**
	 * Starting date
	 * @var Carbon
	 */
	public $from;

	/**
	 * End date
	 * @var Caerbon
	 */
	public $to;

	/**
	 * Whether to save or return the fetched transactions
	 * @var boolean
	 */
	public $save;

	/**
	 * Optional: a list of networks from which transactions should be fetched
	 * @var array|null
	 */
	public $networks;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Carbon $from, Carbon $to, $save, $networks)
	{
		$this->from = $from;
		$this->to = $to;
		$this->save = $save;
		$this->networks = $networks;
	}
}
