<?php namespace Bonsum\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Bonsum\Affiliate\Zanox;
use Bonsum\Affiliate\Affilinet;
use Bonsum\Affiliate\Belboon;
use Bonsum\Affiliate\Adcell;
use Bonsum\Affiliate\Webgains;
use Bonsum\Affiliate\Tradedoubler;
use Bonsum\Affiliate\CJ;
use Bonsum\MerchantTransaction;
use Bonsum\MongoDB\Shop;
use Log;

class MerchantTransactions {


	/**
	 * array of affiliate network data mappers indexed by affiliate network name
	 * @var array
	 */
	protected $networkDrivers;

	public function __construct() {

		$this->networkDrivers = [
			Belboon::NETWORK_NAME => new Belboon,
			Zanox::NETWORK_NAME => new Zanox,
			AffiliNet::NETWORK_NAME . '_1' => new Affilinet(1),
			AffiliNet::NETWORK_NAME . '_2' => new Affilinet(2),
			Adcell::NETWORK_NAME => new Adcell,
			Webgains::NETWORK_NAME => new Webgains,
			Tradedoubler::NETWORK_NAME => new Tradedoubler,
			CJ::NETWORK_NAME => new CJ
		];

	}

	/**
	 * Get the list of all available networks
	 * @return [type] [description]
	 */
	public function getNetworks() {

		return array_keys($this->networkDrivers);
	}

	public function getDriver($network_name) {

		return array_get($this->networkDrivers, $network_name);
	}

	/**
	 * fetch all transactions on a specific date or between a specific date and today
	 * old transactions are replaced with new values
	 * @param  Carbon $from  fetch transactions from this date
	 * @param  Carbon $to  fetch transactions to this date
	 * @param  $save if true, save the transactions in the database, otherwise return an array
	 * @param string|array $network networks to fetch. If null, fetch all networks
	 * @return array of Transaction models or [number of transactions, numer of errors] if $save = TRUE
	 */
	public function fetchTransactions(Carbon $from, Carbon $to, $save = false, $networks = null) {

		if ($to->lt($from)) {
			throw new \Exception(get_class($this) . ': fetchTransactions(): $from must be a date coming before $to.');
		}

		$transaction_counter = 0;
		$error_counter = 0;

		if ($networks) {
			$networks = (array) $networks;
		} else {
			$networks = array_keys($this->networkDrivers);
		}

		$transactions = [];

		foreach ($networks as $network) {

			$date = new Carbon($from);
			$driver = $this->networkDrivers[$network];

			if ($driver->supportsDateRange()) {
				$driver->setDateRange($from, $to);
				$date = new Carbon($to);
			}

			while ($date->lte($to)) {

				if (!$driver->supportsDateRange()) {
					$driver->setDate($date);
				}
				do {
					$last_page = true;
					try {
						$new_transactions = $driver->fetchNextPage($last_page);
						$transaction_counter += count($new_transactions);
						if ($save) {
							foreach ($new_transactions as $transaction) {

								try {
									// Check if we have already download the same transaction
									$existing_transaction = MerchantTransaction::whereNetworkAndNetworkTid(
										$transaction->network,
										$transaction->network_tid
									)->first();

									if ($existing_transaction) {
										$this->checkTransactionChanges($existing_transaction, $transaction);

										$existing_transaction->fill($transaction->getAttributes());
										$transaction = $existing_transaction;
									} else {
										// if this is a new transaction, we fill the original_amount and original_commission field so
										// we can notice when they change
										$transaction->original_commission = $transaction->commission;
										$transaction->original_amount = $transaction->amount;
										$transaction->status_override = MerchantTransaction::STATUS_NONE;
									}

									// if this a new transaction or reward information has not been set yet, we'll set it now.
									// we fetch the reward information (how to compute the reward in bonets for the transaction)
									// from the shop model and we store it in the transaction.
									// In this way we can always retroactively compute the right reward even if the shop model is
									// updated later
									if (!$existing_transaction 	|| $transaction->reward_type === NULL) {
										if ($shop = $transaction->shop) {

											$transaction->reward_type = $shop->reward_type;
											if ($shop->reward_type === Shop::REWARD_TYPE_PROPORTIONAL) {
												$transaction->reward = $shop->proportional_reward;
											} else if ($shop->reward_type === Shop::REWARD_TYPE_FIXED) {
												$transaction->reward = $shop->fixed_reward;
											} else {
												$transaction->reward = 0;
											}
										}
									}

									// set the internal status accordingly...
									$transaction->internal_status =
										($transaction->status_override != MerchantTransaction::STATUS_NONE ?
										$transaction->status_override :
										$driver->mapNetworkStatus($transaction->network_status));

									$transaction->save();
								} catch (\Exception $e) {
									$error_counter++;
									Log::error('Unable to store transaction: ' . $transaction->getNetworkTID());
									Log::error($e);
								}
							}
						} else {
							$transactions = array_merge($transactions, $new_transactions);
						}

					} catch (\Exception $e) {
						$error_counter++;
						Log::error('Unable to fetch transactions for network: ' . $driver::NETWORK_NAME
							. ' range: ' . $from->toDateString() . ' ' . $to->toDateString() . ' (' . $date->toDateString() . ')');
						Log::error($e);
						break;
					}
				} while (!$last_page);

				$date->addDay();
			}
		}

		if (!$save) {
			return $transactions;
		} else {
			return [$transaction_counter, $error_counter];
		}
	}


	protected function checkTransactionChanges(MerchantTransaction $old, MerchantTransaction $new) {

		if ($old->shop_id != $new->shop_id ||
			$old->user_id != $new->user_id ||
			$old->program_id != $new->program_id
		) {
			throw new \Exception(
				"Considerable difference in transactions that should be the same: "
				. json_encode($old, JSON_PRETTY_PRINT)
				. json_encode($new, JSON_PRETTY_PRINT)
			);
		}
	}


}



