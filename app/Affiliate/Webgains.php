<?php namespace Bonsum\Affiliate;

use Bonsum\MerchantTransaction;
use Carbon\Carbon;
use \SoapClient;

class Webgains implements TransactionFetcher, TransactionMapper, Redirector {

	const WSDL_SERVER = 'http://ws.webgains.com/aws.php';
	const NETWORK_NAME = "webgains";
	const DEFAULT_CURRENCY = "EUR";

	protected $from_date;

	protected $to_date;

	protected $soap_client;

	protected $conf;

	public function __construct() {

		$this->conf = config('affiliate.'.self::NETWORK_NAME);
		$this->soap_client = new SoapClient(self::WSDL_SERVER);
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
		$this->setDateRange($date, $to);
	}

	/**
	 * fetch the next page's worth of transactions and increment the page pointer
	 * @param $more_pages boolean  whether there will be more pages to read (false) or this is the last one (true)
	 * @return array array of Transaction models (possibily empty)
	 */
	public function fetchNextPage(&$last_page) {

		if (!$this->from_date || !$this->to_date) {
			throw new \Exception(get_class($this) . ': you have to call setDate or setDateRange before you can fetch any data');
		}

		$transactions = $this->mapTransactionData($this->soap_client->getFullEarnings(
			$this->from_date->toDateString(),
			$this->to_date->toDateString(),
			$this->conf['campaignId'],
			$this->conf['login'],
			$this->conf['password']
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
	 * @param  object|array $data  raw data from the Affiliate API.
	 *                               The format is network-dependent
	 * @return array of Bonsum\Transaction       array of the transaction models
	 */
	public function mapTransactionData($data) {

		$transactions = [];

		if (is_array($data)) {

			foreach ($data as $item) {

				$tdata = [];
				$tdata['network'] = self::NETWORK_NAME;
				$tdata['network_tid'] = object_get($item, "transactionID");
				$tdata['network_status'] = object_get($item, 'paymentStatus');
				$tdata['amount'] = object_get($item, 'saleValue');
				$tdata['currency'] = self::DEFAULT_CURRENCY;
				$tdata['clickdate'] = (empty($item->date) ? null : Carbon::parse(object_get($item, 'date')));
				$tdata['commission'] = object_get($item, 'commission');
				$tdata['program_name'] = object_get($item, 'programName');
				$tdata['program_id'] = object_get($item, 'programID');
				if (preg_match("/shopid(\d+).*userid(\d+)/", object_get($item, 'clickRef'), $matches) === 1) {
					$tdata['shop_id'] = $matches[1];
					$tdata['user_id'] = $matches[2];
				}
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
	public function mapNetworkStatus($status) {

		switch (strtolower($status)) {

			case 'cleared':
			case 'paid':
				return MerchantTransaction::STATUS_CONFIRMED;
			case 'cancelled':
				return MerchantTransaction::STATUS_CANCELED;
			case 'notcleared':
				return MerchantTransaction::STATUS_OPEN;
		}

		return MerchantTransaction::STATUS_NONE;
	}


	public static function makeSubIDLink($network_link, $shop_id, $user_id) {

		$parsed = parse_url($network_link);
		$query = array_get($parsed, 'query');
		$query .= ($query ? '&' : '') . 'clickref=shopid' . urlencode($shop_id) . 'userid' . urlencode($user_id);
		$parsed['query'] = $query;

		return \Bonsum\Helpers\Url::build($parsed);

	}

}
