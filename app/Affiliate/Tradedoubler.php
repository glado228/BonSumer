<?php namespace Bonsum\Affiliate;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\StreamWrapper as GuzzleStreamWrapper;
use Bonsum\MerchantTransaction;

class Tradedoubler implements TransactionFetcher, TransactionMapper {


	const NETWORK_NAME = "tradedoubler";
	const DEFAULT_CURRENCY = "EUR";

	const REPORT_URL = "http://www.tradedoubler.com/pan/aReport3Key.action";

	protected $from_date;

	protected $to_date;

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

		$report_url = self::REPORT_URL . '?'
		. 'reportName=aAffiliateEventBreakdownReport'
		. '&columns=programId'
		. '&columns=timeOfEvent'
		. '&columns=lastModified'
		. '&columns=epi1'
		. '&columns=pendingStatus'
		. '&columns=affiliateCommission'
		. '&columns=leadNR'
		. '&columns=orderNR'
		. '&columns=orderValue'
		. '&columns=eventId'
		. '&columns=siteName'
		. '&startDate=' . $this->from_date->format('d.m.y')
		. '&endDate=' . $this->to_date->format('d.m.y')
		. '&metric1.lastOperator=/'
		. '&currencyId=EUR'
		. '&event_id=0'
		. '&pending_status=1'
		. '&metric1.summaryType=NONE'
		. '&metric1.operator1=/'
		. '&latestDayToExecute=0'
		. '&breakdownOption=1'
		. '&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE'
		. '&metric1.columnName1=orderValue'
		. '&setColumns=true'
		. '&metric1.columnName2=orderValue'
		. '&decorator=popupDecorator'
		. '&metric1.midOperator=/'
		. '&affiliateId='
		. '&dateSelectionType=1'
		. '&sortBy=timeOfEvent'
		. '&customKeyMetricCount=0'
		. '&applyNamedDecorator=true'
		. '&separator='
		. '&format=CSV'
		. '&key=' . $this->conf['key'];

		$response = $this->http_client->get($report_url, [
			'exceptions' => false
		]);

		if ($response->getStatusCode() !== 200) {
			throw new \Exception(get_class($this) . ': status code: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . ' ' . $response->getBody());
		}

		$body = $response->getBody();
		$stream = GuzzleStreamWrapper::getResource($body);
		$last_page = true;

		$line_cnt = 0;
		$data = [];
		while (($line = fgets($stream)) !== FALSE) {
			if ($line_cnt > 1) {
				//skip header
				// convert to utf-8 from an unspecified 8bit character set
				$fields = array_map(function ($el) {
				 	return trim(trim($el, '"'));
				}, explode(';', mb_convert_encoding($line, "UTF-8", "8bit")));

				if (count($fields) === 12 && !empty($fields[0])) {
					$data[] = $fields;
				}
			}
			$line_cnt++;
		}

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
			$tdata['program_name'] = $item[0];
			$tdata['program_id'] = $item[1];

			// using the German date format... pure genius!
			$tdata['clickdate'] = (empty($item[2]) ? null : Carbon::createFromFormat('d.m.y', substr($item[2], 0, 8)));

			// Tradedoubler doesn't provide a unique transaction ID... we have to build one ourselves out of
			// sale, lead, and event IDs
			$lead_id = $item[4];
			$sale_id = $item[5];
			$event_id = $item[7];
			$tdata['network_tid'] = "L" . $lead_id . "S" . $sale_id . "-" . $event_id;
			$tdata['network_status'] = $this->rewriteStatus($item[8]);
			$tdata['amount'] = $this->cleanupCurrency($item[10]);
			$tdata['commission'] = $this->cleanupCurrency($item[11]);
			$tdata['currency'] = self::DEFAULT_CURRENCY;
			if (preg_match("/shopid(\d+).*userid(\d+)/", $item[6], $matches) === 1) {
				$tdata['shop_id'] = $matches[1];
				$tdata['user_id'] = $matches[2];
			}

			$tr = new MerchantTransaction($tdata);
			$tr->_rawData = $item;
			$transactions[] =  $tr;
		}

		return $transactions;
	}

	protected function cleanupCurrency($str) {

		$str = str_replace('.', '', $str);
		return floatval(str_replace(',', '.', $str));
	}

	protected function rewriteStatus($status) {

		switch ($this->mapNetworkStatus($status)) {

			case MerchantTransaction::STATUS_CONFIRMED:
				return 'Confirmed';
			case MerchantTransaction::STATUS_CANCELED:
				return 'Canceled';
			case MerchantTransaction::STATUS_OPEN:
				return 'Open';
		}

		return 'none';
	}

	/**
	 * maps the network status (a string) to the corresponding integer we use internally
	 * @param  string $status the status from the network
	 * @return int    the status as defined by the constants in Bonsum\MerchantTransaction
	 */
	public function mapNetworkStatus($status) {

		switch (strtolower($status)) {

			case 'a':
			case 'confirmed':
				return MerchantTransaction::STATUS_CONFIRMED;
			case 'd':
			case 'canceled':
				return MerchantTransaction::STATUS_CANCELED;
			case 'p':
			case 'open':
				return MerchantTransaction::STATUS_OPEN;
		}
		return MerchantTransaction::STATUS_NONE;
	}


	public static function makeSubIDLink($network_link, $shop_id, $user_id) {

		$parsed = parse_url($network_link);
		$query = array_get($parsed, 'query');
		$query .= ($query ? '&' : '') . 'epi=shopid' . urlencode($shop_id) . 'userid' . urlencode($user_id);
		$parsed['query'] = $query;

		return \Bonsum\Helpers\Url::build($parsed);
	}
}

