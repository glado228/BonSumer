<?php namespace Bonsum\Affiliate;

use SoapClient;
use Bonsum\MerchantTransaction;
use Carbon\Carbon;
use Log;
use Exception;

class Affilinet implements TransactionFetcher, TransactionMapper, Redirector {

	const NETWORK_NAME = 'affilinet';
	const NETWORK_NAME_1 = 'affilinet_1';
	const NETWORK_NAME_2 = 'affilinet_2';

	const WSDL_STATISTICS = 'https://api.affili.net/V2.0/PublisherStatistics.svc?wsdl';
	const WSDL_LOGON = 'https://api.affili.net/V2.0/Logon.svc?wsdl';

	const DEFAULT_CURRENCY = 'EUR';

	/**
	 * the next page that we will fetch
	 * @var int
	 */
	protected $page;

	/**
	 * how many items per page
	 * @var int
	 */
	protected $page_size;

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
	 * the soap client instance that we will be using to communicate with the affiliate network
	 * @var [type]
	 */
	protected $soap_login;


	protected $soap_stats;

	/**
	 * Affilinet access token
	 * @var string
	 */
	protected $access_token;

	/**
	 * Configuration variables
	 * @var array
	 */
	protected $conf;

	/**
	 * The affiliate account number (we have 2)
	 * @var [type]
	 */
	protected $account;


	public function __construct($account = 1, $page_size = 50) {
		$this->account = $account;
		$this->page_size = $page_size;
		$this->conf = config('affiliate.'.self::NETWORK_NAME . '_' . $this->account);
		$this->soap_login = new SoapClient(self::WSDL_LOGON);
		$this->soap_stats = new SoapClient(self::WSDL_STATISTICS);
	}

	public function getNetworkName() {

		return self::NETWORK_NAME . '_' . $this->account;
	}


	public function logIn() {

		$this->access_token = $this->soap_login->Logon([
			'Username' => $this->conf['username'],
			'Password' => $this->conf['password'],
			'WebServiceType' => 'Publisher'
		]);
	}

	public function supportsDateRange() {
		return true;
	}

	public function setDateRange(Carbon $from, Carbon $to) {
		$this->from_date = new Carbon($from);
		$this->to_date = new Carbon($to);
		$this->page = 1;
	}

	public function setDate(Carbon $date) {
		$this->setDateRange($date, $date);
	}

	public function fetchNextPage(&$last_page) {

		if (!$this->from_date || !$this->to_date) {
			throw new \Exception(get_class($this) . ': you have to call setDate or setDateRange before you can fetch any data');
		}

		if (!$this->access_token) {
			$this->logIn();
		}

		$transactions = $this->mapTransactionData($this->soap_stats->GetTransactions([
			'CredentialToken' => $this->access_token,
			'PageSettings' => [
				'CurrentPage' => $this->page,
				'PageSize' => $this->page_size
			],
			'TransactionQuery' => [
				'StartDate' => $this->from_date->toDateString(),
				'EndDate' => $this->to_date->toDateString(),
				'ValuationType' => 'DateOfRegistration',
				'TransactionStatus' => 'All'
			]
		]));

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
				return MerchantTransaction::STATUS_CONFIRMED;
				break;
			case 'cancelled':
				return MerchantTransaction::STATUS_CANCELED;
				break;
		}

		return MerchantTransaction::STATUS_NONE;
	}

	public function mapTransactionData($data) {

		$transactions = [];

		$tarray = object_get($data, 'TransactionCollection.Transaction');


		if ($tarray) {

			// TransactionCollection.Transaction is either an array (in the case of multiple transactions)
			// or an object (in the case of a single transaction) :(
			if (!is_array($tarray)) {
				$tarray = [$tarray];
			}

			foreach ($tarray as $item) {
				$tdata = [];
				$tdata['network'] = $this->getNetworkName();
				$tdata['network_tid'] = object_get($item, 'TransactionId');
				$tdata['network_status'] = object_get($item, 'TransactionStatus');
				$tdata['amount'] = object_get($item, 'NetPrice');
				if (empty($item->ClickDate) && empty($item->CheckDate)) {
					$tdata['clickdate'] = null;
				} else {
					$tdata['clickdate'] = Carbon::parse(object_get($item, 'ClickDate') ?: object_get($item, 'CheckDate'));
				}
				$tdata['commission'] = object_get($item, 'PublisherCommission');
				$tdata['program_name'] = object_get($item, 'ProgramTitle');
				$tdata['program_id'] = object_get($item, 'ProgramId');
				$tdata['currency'] = self::DEFAULT_CURRENCY;
				$subid = object_get($item, 'SubId');
				if (preg_match("/shopid(\d+).*userid(\d+)/", $subid, $matches) === 1) {
					$tdata['shop_id'] = $matches[1];
					$tdata['user_id'] = $matches[2];
				}
				//sscanf($subid, 'shopid%duserid%d', $tdata['shop_id'], $tdata['user_id']);
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
		$query .= ($query ? '&' : '') . 'subid=shopid' . urlencode($shop_id) . 'userid' . urlencode($user_id);
		$parsed['query'] = $query;

		return \Bonsum\Helpers\Url::build($parsed);
	}
}
