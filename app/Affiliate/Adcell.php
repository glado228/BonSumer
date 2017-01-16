<?php namespace Bonsum\Affiliate;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\StreamWrapper as GuzzleStreamWrapper;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Bonsum\MerchantTransaction;

class Adcell implements TransactionFetcher, TransactionMapper, Redirector {

	const NETWORK_NAME = 'adcell';

	const REPORT_URL_BASE = 'https://www.adcell.de/csv_affilistats.php';
	const PROGRAM_NAME_URL_BASE = 'https://www.adcell.de/partnerprogramme';

	const DEFAULT_CURRENCY = 'EUR';

	/**
	 * Guzzle http client
	 * @var GuzzleHttp\Client
	 */
	protected $http_client;

	/**
	 * [$from_date description]
	 * @var [type]
	 */
	protected $from_date;

	/**
	 * [$to_date description]
	 * @var [type]
	 */
	protected $to_date;

	/**
	 * Ad cell does not return the program name, only the id
	 * we have to perform a second call to retrieve the name(s), and we cache them here.
	 * @var arrray
	 */
	protected $program_name = [];


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
		$this->setDateRange($date, (new Carbon($date))->addDay());
	}

	/**
	 * fetch the next page's worth of transactions and increment the page pointer
	 * @param $more_pages boolean  whether there will be more pages to read (false) or this is the last one (true)
	 * @return array array of Transaction models (possibily empty)
	 */
	public function fetchNextPage(&$last_page) {

		$report_url =
			self::REPORT_URL_BASE . '?' .
			http_build_query([
				'sarts' => 'x',
				'pid' => 'a',
				'status' => 'a',
				'subid' => '',
				'eventid' => 'a',
				'timestart' => $this->from_date->getTimestamp(),
				'timeend' => $this->to_date->getTimestamp(),
				'uname' => $this->conf['username'],
				'pass' => $this->conf['password']
			]);

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

			if ($line_cnt > 0) {
				//skip header
				// adcell strings are not UTF-8, convert them to utf-8 from an unspecified 8bit character set
				$fields = array_map(function ($el) {
				 	return trim(trim($el, '"'));
				}, explode(';', mb_convert_encoding($line, "UTF-8", "8bit")));

				if (count($fields) === 12 && !starts_with($fields[0], 'Gesamt')) {
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
			$tdata['network_tid'] = $item[0];
			$tdata['clickdate'] = Carbon::parse($item[1]);
			$tdata['program_id'] = $item[3];
			$tdata['program_name'] = $this->fetchProgramName($tdata['program_id']);
			$tdata['network_status'] = $item[7];
			$tdata['amount'] = $this->cleanupCurrency($item[8]);
			$tdata['commission'] = $this->cleanupCurrency($item[9]);
			$tdata['currency'] = self::DEFAULT_CURRENCY;
			/*
				We first try to extract tracking information from the SubId field
				If that does not work, we resort to the referer field
			 */
			if (preg_match("/shopid(\d+).*userid(\d+)/", $item[6], $matches) === 1) {
				$tdata['shop_id'] = $matches[1];
				$tdata['user_id'] = $matches[2];

			} else if (preg_match("/referal\[shop_id\]=(\d+)&referal\[user_id\]=(\d+)/", $item[10], $matches) === 1) {
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
	 * Adcell returns currencies with European number formats. This function
	 * simply removes dots and replaces the comma with a dot.
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */

	protected function cleanupCurrency($str) {

		$str = str_replace('.', '', $str);
		return floatval(str_replace(',', '.', $str));
	}


	/**
	 * maps the network status (a string) to the corresponding integer we use internally
	 * @param  string $status the status from the network
	 * @return int    the status as defined by the constants in Bonsum\MerchantTransaction
	 */
	public function mapNetworkStatus($status) {

		switch (mb_strtolower($status, "UTF-8")) {
			case "offen":
				return MerchantTransaction::STATUS_OPEN;
				break;
			case "best\xc3\xa4tigt":
				return MerchantTransaction::STATUS_CONFIRMED;
				break;
			default:
				return MerchantTransaction::STATUS_CANCELED;
				break;
		}

		return MerchantTransaction::STATUS_NONE;
	}

	/**
	 * Horrible! call up a web page on ad cell to get the program name. Why don't they have a decent API?
	 * @param  int $program_id the id of the program
	 * @return string             the name of the program
	 */
	protected function fetchProgramName($program_id) {

		if (!empty($this->program_name[$program_id])) {
			return $this->program_name[$program_id];
		}

		$program_name = NULL;
		$report_url = self::PROGRAM_NAME_URL_BASE . '/' . $program_id;

		$response = $this->http_client->get($report_url, [
			'exceptions' => false
		]);
		if ($response->getStatusCode() === 200) {
			$crawler = new DomCrawler($response->getBody()->getContents());
			$keywords_tag = $crawler->filter('meta[name="keywords"]');
			if ($keywords_tag->count() > 0) {
				$program_name = explode(',', $keywords_tag->attr('content'))[0];
			}
			if (!$program_name) {
				$program_name = self::NETWORK_NAME . ' ' . $program_id;
			}
			$this->program_name[$program_id] = $program_name;
		}
		return $program_name;
	}


	public static function makeSubIDLink($network_link, $shop_id, $user_id) {

		$parsed = parse_url($network_link);
		$parsed['path'] .= '/subId/shopid' . urlencode($shop_id) . 'userid' . urlencode($user_id);

		return \Bonsum\Helpers\Url::build($parsed);
	}

}
