<?php namespace Bonsum\Services;

use Bonsum\User;
use Bonsum\MerchantTransaction;
use Bonsum\Events\BonetsUpdateNotCommitted;
use DB;

class Bonets {

	/**
	 * Maps currency to bonets per unit
	 * @var array
	 */
	protected $bonets_per_currency_unit_map;

	/**
	 * map currency codes to symbols
	 * @var array
	 */
	protected $currency_symbols_map;


	public function __construct() {
		$this->bonets_per_currency_unit_map = config('bonets.bonets_per_currency_unit');
		$this->currency_symbols_map = config('bonets.currency_symbols');
	}


	/**
	 * return the symbol corresponding to a currency code
	 * @param  string|null $curr_code  the currency code. If null, defaults to the result of getCurrency()
	 * @return string            HTML symbol
	 */
	public function formatCurrencySymbol($curr_code = null) {

		if (!$curr_code) {
			$curr_code = $this->getCurrency();
		}

		return $this->currency_symbols_map[$curr_code];
	}


	/**
	 * Format an amount using the given currency code and locale
	 * @param  flaot $value
	 * @param  string|null $curr_code  currency code. If null, defaults to the result of getCurrency()
	 * @return string            HTML description of the amount
	 */
	public function formatCurrency($value, $curr_code = null) {

		if (!$curr_code) {
			$curr_code = $this->getCurrency();
		}

		return trans('general.amount', ['amount' => $value, 'curr' => $this->currency_symbols_map[$curr_code]]);
	}


	public function getBonetsPerCurrencyUnit($curr_code = null) {

		if (!$curr_code) {
			$curr_code = $this->getCurrency();
		}

		return $this->bonets_per_currency_unit_map[$curr_code];
	}

	/**
	 * Get list of supported currencies
	 * @return [type] [description]
	 */
	public function getCurrencies() {

		return array_keys($this->currency_symbols_map);
	}


	/**
	 * determine the currency in use based on the locale
	 * @return string
	 */
	public function getCurrency() {
		return 'EUR';
	}

	/**
	 * Computed the amount of bonets credited to the user based on a commission
	 * @param  [type] $amount [description]
	 * @return [type]         [description]
	 */
	public function convertCommissionToBonets($amount, $bonets_per_currency_unit) {

		return intval(round($amount/2.0 * $bonets_per_currency_unit));
	}


	/**
	 * Convert an amount to bonets. Rounded to nearest integer
	 * @param  [type] $amount [description]
	 * @return [type]         [description]
	 */
	public function toBonets($amount, $curr_code = NULL) {

		return intval(round($amount * $this->getBonetsPerCurrencyUnit($curr_code)));
	}

	/**
	 * Converts bonets to an amount. Rounded up to 2 decimal digits
	 * @param  [type] $bonets [description]
	 * @return [type]         [description]
	 */
	public function fromBonets($bonets, $curr_code = NULL) {

		return round($bonets / $this->getBonetsPerCurrencyUnit($curr_code), 2);
	}


	public function updateUserBonets(User $user) {

		$total_bonets = 0;

		/*
			If a user has many transactions, donations, redeems, or credits,
			the following code might take up a lot of memory, because the relations
			are loaded in one block and stored in memory

			This should probably be replaced with explicit joins and chunking of results

			Max
			2015/6/18
		 */

		$user->merchant_transactions->each(function ($tr) use (&$total_bonets) {

			if ($tr->internal_status === MerchantTransaction::STATUS_CONFIRMED) {

				$total_bonets += $tr->bonets;
			}
		});

		$user->bonets_credits->each(function ($rd) use (&$total_bonets) {

			$total_bonets += $rd->bonets;
		});

		$user->bonets_redeems->each(function ($rd) use (&$total_bonets) {

			$total_bonets -= $rd->bonets;
		});

		$user->bonets_donations->each(function ($rd) use (&$total_bonets) {

			$total_bonets -= $rd->bonets;
		});

		DB::transaction(function() use ($user, $total_bonets) {

			$temp = User::where('id', '=', $user->id)->lockForUpdate()->first();
			if ($user->bonets === $temp->bonets) {
				$user->bonets = $total_bonets;
				$user->save();
			} else {
				event(new BonetsUpdateNotCommitted($user, $user->bonets, $total_bonets, $temp->bonets));
			}
		});

		return $total_bonets;
	}
}
