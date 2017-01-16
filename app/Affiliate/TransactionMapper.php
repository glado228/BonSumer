<?php namespace Bonsum\Affiliate;



interface TransactionMapper {

	/**
	 * map an affiliate-specific transaction data to a Bonsum\Transaction models
	 * @param  object|array $data  raw data from the Affiliate API.
	 *                               The format is network-dependent
	 * @return array of Bonsum\Transaction       array of the transaction models
	 */
	public function mapTransactionData($data);

	/**
	 * maps the network status (a string) to the corresponding integer we use internally
	 * @param  string $status the status from the network
	 * @return int    the status as defined by the constants in Bonsum\MerchantTransaction
	 */
	public function mapNetworkStatus($status);
}
