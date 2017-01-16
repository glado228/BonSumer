<?php namespace Bonsum\Http\Controllers\Admin;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;
use Bonsum\Services\FrontEnd;
use Illuminate\Http\Request;
use Bonsum\BonetsDonation;
use Bonsum\Helpers\Csv;
use Bonsum\Services\Bonets;
use Bonsum\Helpers\GridFilter;
use App;

class DonationController extends Controller {


	const DEFAULT_PAGE_SIZE = 50;

	public function __construct() {

		$this->middleware('admin');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(FrontEnd $fe, Bonets $bonets_service, $user_id = NULL)
	{

		$fe->addVars([
			'donations' => [
				'loadURL' => action('Admin\DonationController@loadDonations', ['user_id' => $user_id]),
				'pageSize' => self::DEFAULT_PAGE_SIZE,
				'currencies' => $bonets_service->getCurrencies(),
				'singleUserURL' => action('Admin\UserController@showSingleUser', NULL)
			]
		]);

		return view('admin.donation')->with([
			'user_id' => $user_id
		]);
	}


	public function loadDonations(Request $request, $user_id = NULL) {

		$this->validate($request, [
			'startRow' => 'integer|required_without:csv',
			'endRow' => 'integer|required_without:csv',
			'sortModel' => 'array',
			'filterModel' => 'array'
		]);

		$donations = BonetsDonation::query();
		if ($user_id) {
			$donations->where('user_id', '=', $user_id);
		}

		if ($filterModel = $request->get('filterModel')) {
			foreach ($filterModel as $field => $filter) {

				switch ($field) {

					case 'amount':
					case 'bonets':
						GridFilter::number($donations, $field, $filter);
						break;
					default:
						GridFilter::set($donations, $field, $filter);
						break;
				}
			}
		}

		if ($sortModel = $request->input('sortModel')) {
			foreach ($sortModel as $sort) {
				$donations->orderBy($sort['field'], $sort['sort']);
			}
		}

		if ($request->input('csv')) {

			return Csv::sendCsvFileFromQuery($donations, 'donations',
				['receiver', 'user_id', 'date', 'amount', 'currency', 'bonets']);

		} else {

			$start = intval($request->input('startRow'));
			$end = intval($request->input('endRow'));
			$lastRow = $donations->count();

			return response()->json([
				'rowsThisPage' => $donations->skip($start)->take($end-$start)->get(),
				'lastRow' => $lastRow
			]);
		}
	}



}
