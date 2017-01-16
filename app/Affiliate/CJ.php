<?php namespace Bonsum\Affiliate;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\StreamWrapper as GuzzleStreamWrapper;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Bonsum\MerchantTransaction;

class CJ implements TransactionFetcher, TransactionMapper, Redirector {

	const NETWORK_NAME = 'cj';
	const REPORT_URL = 'https://commission-detail.api.cj.com/v3/commissions';

	const DEFAULT_CURRENCY = 'EUR';

	/*
		Commission junction places limit on the time span that can be queried with one request:

		"Invalid date interval: only 1 to 31 days period is allowed."

		We conservatively pick 20 days
	 */
	const MAX_DAYS = 20;


	protected $from_date;

	protected $to_date;

	protected $current_from_date;

	protected $conf;

	protected $http_client;


	public function __construct() {

		$this->conf = config('affiliate.'.self::NETWORK_NAME);
		$this->http_client = new GuzzleClient();
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
	 * @param $more_pages boolean  whether there will be more pages to read (false) or this is the last one (true)
	 * @return array array of Transaction models (possibily empty)
	 */
	public function fetchNextPage(&$last_page) {

		if (!$this->from_date || !$this->to_date) {
			throw new \Exception(get_class($this) . ': you have to call setDate or setDateRange before you can fetch any data');
		}

		if (!$this->current_from_date) {
			$this->current_from_date = new Carbon($this->from_date);
		}

		if ($this->current_from_date->diffInDays($this->to_date) <= self::MAX_DAYS) {
			$last_page = true;
			$to = $this->to_date->copy();
		} else {
			$last_page = false;
			$to = $this->current_from_date->copy()->addDays(self::MAX_DAYS);
		}

		$report_url = self::REPORT_URL . '?'
		. http_build_query(
			[
				'date-type' => 'event',
				'start-date' => $this->current_from_date->toDateString(),
				'end-date' => $to->toDateString(),
				'website-ids' => $this->conf['pid']
			]
		);

		$response = $this->http_client->get($report_url, [
			'headers' => [
				'Authorization' => $this->conf['devkey']
			],
			'exceptions' => false
		]);

		if ($response->getStatusCode() !== 200) {
			throw new \Exception(get_class($this) . ': status code: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . ' ' . $response->getBody());
		}

		if (!$last_page) {
			$this->current_from_date->addDays(self::MAX_DAYS);
		}

		$data = [];
		$crawler = new DomCrawler($response->getBody()->getContents());
		$commissions = $crawler->filter('commission');
		$commissions->each(function(DomCrawler $commission) use (&$data) {
			$item = [];
			$children = $commission->children();
			$children->each(function(DomCrawler $node) use (&$data, &$item) {
				$item[$node->nodeName()] = $node->text();
			});
			$data[] = $item;
		});

		return $this->mapTransactionData($data);
	}

	/**
	 * set the date range from which we are going to fetch transactions (if supported)
	 * @param Carbon $from
	 * @param Carbon $to
	 */
	public function setDateRange(Carbon $from, Carbon $to) {
		$this->from_date = new Carbon($from);
		$this->to_date = new Carbon($to);
		$this->current_from_date = NULL;
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

		foreach ($data as $item) {

			$tdata=[];
			$tdata['network'] = self::NETWORK_NAME;
			$tdata['program_name'] = array_get($item, 'advertiser-name');
			$tdata['program_id'] = array_get($item, 'cid');
			$tdata['clickdate'] = (empty($item['event-date']) ? null : Carbon::parse(array_get($item, 'event-date')));
			$tdata['network_tid'] = array_get($item, 'order-id');
			$tdata['network_status'] = array_get($item, 'action-status');
			$tdata['amount'] = array_get($item, 'sale-amount');
			$tdata['commission'] = array_get($item, 'commission-amount');
			$country = array_get($item, 'country');
			$tdata['currency'] = (strcasecmp($country, 'DE') ? $country : self::DEFAULT_CURRENCY);
			if (preg_match("/shopid(\d+).*userid(\d+)/", array_get($item, 'sid'), $matches) === 1) {
				$tdata['shop_id'] = $matches[1];
				$tdata['user_id'] = $matches[2];
			}
			$tr = new MerchantTransaction($tdata);
			$tr->_rawData = $item;
			$transactions[] =  $tr;
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

			case 'closed':
			case 'locked':
				return MerchantTransaction::STATUS_CONFIRMED;
			case 'open':
				return MerchantTransaction::STATUS_OPEN;
		}

		return MerchantTransaction::STATUS_NONE;
	}

	public static function makeSubIDLink($network_link, $shop_id, $user_id) {

		$parsed = parse_url($network_link);
		$query = array_get($parsed, 'query');
		$query .= ($query ? '&' : '') . 'SID=shopid' . urlencode($shop_id) . 'userid' . urlencode($user_id);
		$parsed['query'] = $query;

		return \Bonsum\Helpers\Url::build($parsed);
	}

}
