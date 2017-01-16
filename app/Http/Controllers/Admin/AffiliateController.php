<?php namespace Bonsum\Http\Controllers\Admin;

use Bonsum\Http\Controllers\Controller;
use Bonsum\MerchantTransaction;
use Bonsum\Services\FrontEnd;
use Illuminate\Http\Request;
use Bonsum\Helpers\Csv;
use Bonsum\Helpers\GridFilter;
use Bonsum\Services\MerchantTransactions;
use Bonsum\Services\Bonets;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AffiliateController extends Controller {


	const DEFAULT_PAGE_SIZE = 15;

	public function __construct() {
		$this->middleware('admin');
	}


	public function loadTransactions(Request $request, $user_id = NULL) {

		$this->validate($request, [
			'startRow' => 'integer|required_without:csv',
			'endRow' => 'integer|required_without:csv',
			'sortModel' => 'array',
			'filterModel' => 'array'
		]);

		$transactions = MerchantTransaction::query();
		if ($user_id) {
			$transactions->where('user_id', '=', $user_id);
		}

		if ($filterModel = $request->get('filterModel')) {
			foreach ($filterModel as $field => $filter) {

				switch ($field) {

					case 'program_name':
						GridFilter::text($transactions, $field, $filter);
						break;
					case 'amount':
					case 'commission':
					case 'original_amount':
					case 'original_commission':
					case 'bonets':
						GridFilter::number($transactions, $field, $filter);
						break;
					default:
						GridFilter::set($transactions, $field, $filter);
						break;
				}
			}
		}

		if ($sortModel = $request->input('sortModel')) {
			foreach ($sortModel as $sort) {
				$transactions->orderBy($sort['field'], $sort['sort']);
			}
		}


		if ($request->input('csv')) {

			return Csv::sendCsvFileFromQuery($transactions, 'transactions',
						[
                                'network',
                                'program_name',
                                'user_id',
                                'shop_id',
                                'clickdate',
                                'amount',
                                'commission',
                                'original_amount',
                                'original_commission',
                                'currency',
                                'status'
                        ]
				);

		} else {

			$start = intval($request->input('startRow'));
			$end = intval($request->input('endRow'));
			$lastRow = $transactions->count();

			return response()->json([
				'rowsThisPage' => $transactions->skip($start)->take($end-$start)->get(),
				'lastRow' => $lastRow
			]);
		}
	}


	public function showTransactions(FrontEnd $fe, MerchantTransactions $tr_service, Bonets $bonets_service, $user_id = NULL) {

		$fe->addVars([
			'merchant_transactions' => [
				'loadURL' => action('Admin\AffiliateController@loadTransactions', ['user_id' => $user_id]),
				'updateStatusOverrideURL' => action('Admin\AffiliateController@updateStatusOverride', NULL, NULL),
				'singleUserURL' => action('Admin\UserController@showSingleUser', NULL),
				'pageSize' => self::DEFAULT_PAGE_SIZE,
				'affiliateNetworks' => $tr_service->getNetworks(),
				'currencies' => $bonets_service->getCurrencies(),
				'status' => [
					MerchantTransaction::STATUS_NONE => 'NONE',
					MerchantTransaction::STATUS_OPEN => 'OPEN',
					MerchantTransaction::STATUS_CONFIRMED => 'CONFIRMED',
					MerchantTransaction::STATUS_CANCELED => 'CANCELED'
				]
			]
		]);

		return view('admin.affiliate')->with([
			'user_id' => $user_id
		]);
	}


	public function updateStatusOverride(MerchantTransactions $ts, $transaction_id, $status_override) {

		if (!in_array($status_override, MerchantTransaction::getValidStates())) {
			throw new BadRequestHttpException('invalid transaction state ' . $status_override);
		}

		$trans = MerchantTransaction::findOrFail($transaction_id);
		$trans->status_override = $status_override;

		$driver = $ts->getDriver($trans->network);
		if (!$driver || !$trans->network) {
			throw new \Exception('failed to retrieve the network driver for transaction with id '. $trans->id);
		}

		// set the internal status accordingly...
		$trans->internal_status =
			($trans->status_override != MerchantTransaction::STATUS_NONE ?
			$trans->status_override :
			$driver->mapNetworkStatus($trans->network_status));

		$trans->save();
	}
}
