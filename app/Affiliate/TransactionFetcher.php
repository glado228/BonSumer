<?php namespace Bonsum\Affiliate;

use Carbon\Carbon;


interface TransactionFetcher {

	/**
	 * Log in
	 */
	public function logIn();

	/**
	 * set the date for which we are going to fetch transactions, set the page pointer to zero
	 * @param Carbon $date
	 */
	public function setDate(Carbon $date);

	/**
	 * fetch the next page's worth of transactions and increment the page pointer
	 * @param $more_pages boolean  whether there will be more pages to read (false) or this is the last one (true)
	 * @return array array of Transaction models (possibily empty)
	 */
	public function fetchNextPage(&$last_page);

	/**
	 * set the date range from which we are going to fetch transactions (if supported)
	 * @param Carbon $from
	 * @param Carbon $to
	 */
	public function setDateRange(Carbon $from, Carbon $to);

	/**
	 * Wehter this fetcher implementation supports data ranges
	 * @return boolean
	 */
	public function supportsDateRange();
}
