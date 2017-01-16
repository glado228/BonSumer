<?php namespace Bonsum\Affiliate;

use Bonsum\MerchantTransaction;
use Carbon\Carbon;
use Exception;

class Zanox implements TransactionMapper, TransactionFetcher, Redirector {

	const NETWORK_NAME = 'zanox';

	/**
	 * Zanox API
	 * @var \Zanox\Api\MethodsInterface
	 */
	protected $api;

	/**
	 * The next page we are going to fetch
	 * @var int
	 */
	protected $page;

	/**
	 * The next date we are going to fetch
	 * @var Carbon\Carbon
	 */
	protected $date;

	/**
	 * how many transactions per page we want to fetch
	 * @var integer
	 */
	protected $page_size;

	/**
	 * Configuration variables
	 * @var array
	 */
	protected $conf;


	public function __construct($page_size = 50) {

		$this->page_size = $page_size;
		$this->conf = config('affiliate.'.self::NETWORK_NAME);
		$this->api = \Zanox\ApiClient::factory([
			'protocol' => \Zanox\Api\Constants::PROTOCOL_JSON,
			'interface' => \Zanox\Api\Constants::RESTFUL_INTERFACE
		]);

		$this->logIn();
	}

	public function logIn() {

		$this->api->setConnectId($this->conf['connect_id']);
		$this->api->setSecretKey($this->conf['secret_key']);
	}

	public function supportsDateRange() {
		return false;
	}

	public function setDateRange(Carbon $from, Carbon $to) {
		throw new \Exception(get_class($this) . ': date range not supported');
	}

	/**
	 * Prepare to fetch the first page on a given date
	 * @param  Carbon $date
	 */
	public function setDate(Carbon $date) {

		$this->date = new Carbon($date);
		$this->page = 0;
	}

	/**
	 * fetch the next page's worth of transactions
	 * @return array  array of transactions
	 */
	public function fetchNextPage(&$last_page) {

		$transactions = [];

		if (!$this->date) {
			throw new \Exception(get_class($this) . ': you need to call setDate() before fetching any transactions');
		}

		$data = json_decode($this->api->getSales($this->date->toDateString(), NULL, NULL, NULL, NULL, $this->page, $this->page_size), true);
		$transactions = array_merge(
			$transactions,
			$this->mapTransactionData($data)
		);

		$data = json_decode($this->api->getLeads($this->date->toDateString(), NULL, NULL, NULL, NULL, $this->page, $this->page_size), true);
		$transactions = array_merge(
			$transactions,
			$this->mapTransactionData($data)
		);

		$this->page++;

		$last_page = empty($transactions);

		return $transactions;
	}

	public function mapNetworkStatus($status) {

		switch (strtolower($status)) {
			case 'open':
				return MerchantTransaction::STATUS_OPEN;
				break;
			case 'confirmed':
			case 'approved':
				return MerchantTransaction::STATUS_CONFIRMED;
				break;
			case 'rejected':
				return MerchantTransaction::STATUS_CANCELED;
				break;
		}

		return MerchantTransaction::STATUS_NONE;
	}

	public function mapTransactionData($data) {

		$transactions = [];

		$items = array_get($data,'saleItems.saleItem');

		if (is_array($items)) {
			foreach ($items as $item) {

				$tdata = [];
				$tdata['network'] = self::NETWORK_NAME;
				$tdata['network_tid'] = array_get($item, "@id");
				$tdata['network_status'] = array_get($item, 'reviewState');
				$tdata['amount'] = array_get($item, 'amount');
				$tdata['currency'] = array_get($item, 'currency');
				if (empty($item['clickDate']) && empty($item['trackingDate'])) {
					$tdata['clickdate'] = null;
				} else {
					$tdata['clickdate'] = Carbon::parse(array_get($item, 'clickDate') ?: array_get($item, 'trackingDate'));
				}
				$tdata['commission'] = array_get($item, 'commission');
				$tdata['program_name'] = array_get($item, 'program.$');
				$tdata['program_id'] = array_get($item, 'program.@id');
				$gpps = array_get($item, 'gpps.gpp');
				if (is_array($gpps)) {
					foreach ($gpps as $gpp) {
						if (is_array($gpp)) {
							$id = array_get($gpp, '@id');
							if ($id === 'zpar0') {
								$tdata['shop_id'] = array_get($gpp, '$');
							} else if ($id === 'zpar1') {
								$tdata['user_id'] = array_get($gpp, '$');
							}
						}
					}
				}

				$tr = new MerchantTransaction($tdata);
				$tr->_rawData = $item;
				$transactions[] = $tr;
			}
		}

		return $transactions;
	}

	public static function makeSubIDLink($network_link, $shop_id, $user_id) {

		$parsed = parse_url($network_link);
		$query = array_get($parsed, 'query');
		$query .= ($query ? '&' : '') . http_build_query([
			'zpar0' => '[['. $shop_id . ']]',
			'zpar1' => '[['. $user_id . ']]'
		]);
		$parsed['query'] = $query;

		return \Bonsum\Helpers\Url::build($parsed);

	}

}
