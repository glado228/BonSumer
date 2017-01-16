<?php namespace Bonsum\Commands;

use Bonsum\Commands\Command;
use Bonsum\MongoDB\Shop;

class RedeemBonets extends Command {


	public $user_id;

	public $amount;

	public $shop;

	public $voucher_code;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($user_id, $amount, Shop $shop, $voucher_code)
	{
		$this->user_id = $user_id;
		$this->amount = $amount;
		$this->shop = $shop;
		$this->voucher_code = $voucher_code;
	}

}
