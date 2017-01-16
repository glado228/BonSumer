<?php namespace Bonsum\Affiliate;

use SoapClient;
use Carbon\Carbon;
use Bonsum\MerchantTransaction;

class Belboon implements TransactionFetcher, TransactionMapper, Redirector {

	const NETWORK_NAME = "belboon";

	const WSDL_SERVER = 'https://api.belboon.com/?wsdl';


	protected $from_date;

	protected $to_date;


	protected $soap_client;


	public function __construct() {
		$conf = config('affiliate.'.self::NETWORK_NAME);
		$this->soap_client = new SoapClient(self::WSDL_SERVER, $conf);
	}

	/**
	 * Log in
	 */
	public function logIn() {}

	/**
	 * set the date for which we are going to fetch transactions, set the page pointer to zero
	 * @param Carbon $date
	 */
	public function setDate(Carbon $date) {
		$this->setDateRange($date, $date);
	}

	/**
	 * fetch the next page's worth of transactions and increment the page pointer
	 * @return array array of Transaction models (possibily empty)
	 */
	public function fetchNextPage(&$last_page) {

		if (!$this->from_date || !$this->to_date) {
			throw new \Exception(get_class($this) . ': you have to call setDate or setDateRange before you can fetch any data');
		}

		$transactions = $this->mapTransactionData($this->soap_client->getEventList(
			null,
			null,
			null,
			null,
			null,
			$this->from_date->toDateString(),
			$this->to_date->toDateString(),
			null,
			null,
			null,
			null,
			0
		));

		$last_page = true;

		return $transactions;
	}

	/**
	 * set the date range from which we are going to fetch transactions (if supported)
	 * @param Carbon $from
	 * @param Carbon $to
	 */
	public function setDateRange(Carbon $from, Carbon $to) {
		$this->from_date = new Carbon($from);
		$this->to_date = new Carbon($to);
	}

	/**
	 * Wehter this fetcher implementation supports data ranges
	 * @return boolean
	 */
	public function supportsDateRange() {
		return true;
	}

	/**
	 * map an affiliate-specific transaction data to a Bonsum\Transaction models
	 * @param  object|array $batches array or object containing the raw data batches from the Affiliate API.
	 *                               The format is network-dependent
	 * @return array of Bonsum\Transaction       array of the transaction models
	 */
	function mapTransactionData($data) {

		$tarray = object_get($data, 'handler.events');

		$transactions = [];

		if (is_array($tarray)) {

			foreach ($tarray as $item) {

				$tdata = [];
				$tdata['network'] = self::NETWORK_NAME;
				$tdata['network_tid'] = array_get($item, "eventid");
				$tdata['network_status'] = array_get($item, 'eventstatus');
				$tdata['amount'] = array_get($item, 'netvalue');
				$tdata['currency'] = array_get($item, 'eventcurrency');
				$tdata['clickdate'] = (empty($item['eventdate']) ? null : Carbon::parse(array_get($item, 'eventdate')));
				$tdata['commission'] = array_get($item, 'eventcommission');
				$tdata['program_name'] = array_get($item, 'programname');
				$tdata['program_id'] = array_get($item, 'programid');
				$subid = array_get($item, 'subid');
				if (preg_match("/shopid(\d+).*userid(\d+)/", $subid, $matches) === 1) {
					$tdata['shop_id'] = $matches[1];
					$tdata['user_id'] = $matches[2];
				}
				//sscanf($subid, 'subid=shopid%duserid%d', $tdata['shop_id'], $tdata['user_id']);
				$tr = new MerchantTransaction($tdata);
				$tr->_rawData = $item;
				$transactions[] = $tr;
			}
		}

		return $transactions;
	}

	/**
	 * maps the network status (a string) to the corresponding integer we use internally
	 * @param  string $status the status from the network
	 * @return int    the status as defined by the constants in Bonsum\MerchantTransaction
	 */
	function mapNetworkStatus($status) {

		switch (strtolower($status)) {
			case 'pending':
				return MerchantTransaction::STATUS_OPEN;
				break;
			case 'approved':
				return MerchantTransaction::STATUS_CONFIRMED;
				break;
			case 'rejected':
				return MerchantTransaction::STATUS_CANCELED;
				break;
		}

		return MerchantTransaction::STATUS_NONE;
	}


	public static function makeSubIDLink($network_link, $shop_id, $user_id) {

		$parsed = parse_url($network_link);
		$query = array_get($parsed, 'query');
		$parsed['path'] .= '/subid=shopid' . urlencode($shop_id) . 'userid' . urlencode($user_id);

		return \Bonsum\Helpers\Url::build($parsed);
	}

}
