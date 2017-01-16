<?php namespace Bonsum\Http\Controllers;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;
use Bonsum\Services\DonationOption as DonationService;
use Bonsum\Services\Shop as ShopService;
use Bonsum\MongoDB\Shop;
use Bonsum\MongoDB\Donation;
use Bonsum\Services\FrontEnd;
use Bonsum\Commands\DonateBonets;
use Bonsum\Commands\RedeemBonets;
use Carbon\Carbon;
use Bonsum\Services\Bonets;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Validator;
use App;
use DB;



class RedeemController extends Controller {


	const INITIALLY_LOADED_OPTIONS = 9;

	const REDEEM_TYPE_BONSUMING = 0;
	const REDEEM_TYPE_DONATING = 1;

	public function __construct(ShopService $shop_service, DonationService $donation_service) {
		$this->middleware('admin', ['except' => ['show', 'index', 'fetch', 'donate', 'getVoucher']]);
		$this->middleware('auth', ['only' => ['getVoucher', 'donate']]);
		$this->shop_service = $shop_service;
		$this->donation_service = $donation_service;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, Frontend $fe, Guard $guard)
	{
		return $this->showOptions($request, $fe, $guard, true);
	}

	public function indexInvisible(Request $request, Frontend $fe, Guard $guard)
	{
		return $this->showOptions($request, $fe, $guard, false);
	}


	protected function showOptions(Request $request, Frontend $fe, Guard $guard, $visible = true) {

		/*
		Don't need to pre-load options... they will be laoded by the JS frontend
		 */
		//$options = $this->retrieveOptions($request->all(), 0, self::INITIALLY_LOADED_OPTIONS, $totalOptions, $visible);

		$admin_view = $guard->user() && $guard->user()->admin;

		$fe->addResource([
			'redeem.donation_subject' => trans('redeem.donation_subject'),
			'redeem.donation_success_coda' => trans('redeem.donation_success_coda'),
			'redeem.voucher_success_coda' => trans('redeem.voucher_success_coda', ['account_url' => action('AccountController@index')]),
			'redeem.voucher_subject' => trans('redeem.voucher_subject')

		], \Bonsum\Services\Resource::RESOURCE_TYPE_TEXT);

		$fe->addVars([
			'optionsFetchUrl' => action('RedeemController@fetch' . (!$visible ? 'Invisible' : '')),
			'donateUrl' => action('RedeemController@donate', ['option_id' => NULL]),
			'getVoucherUrl' => action('RedeemController@getVoucher', ['shop_id' => NULL]),
			/*'options' => $options,
			'totalOptions' => $totalOptions,*/
			'REDEEM_TYPE_BONSUMING' => self::REDEEM_TYPE_BONSUMING,
			'REDEEM_TYPE_DONATING' => self::REDEEM_TYPE_DONATING,
			'shopCriteriaMap' => array_flip(Shop::$shopCriteria)
		]);

		if ($admin_view) {
			$fe->addVars([
				'optionDeleteUrl' => action('RedeemController@destroy', NULL),
				'optionSetVisibilityUrl' => action('RedeemController@setVisibility', NULL)
			]);
		}

		return view('redeem.index')->with([
			'visible' => $visible,
			'title_tag' => trans('seo.redeem.index.title_tag', ['search' => $request->input('searchString') ?: 'Bonsum']),
			'meta_description' => trans('seo.redeem.index.meta_description', ['search' => $request->input('searchString') ?: 'Bonsum'])
		]);
	}


	public function fetch(Request $request) {
		return $this->fetchOptions($request, true);
	}

	public function fetchInvisible(Request $request) {
		return $this->fetchOptions($request, false);
	}


	protected function fetchOptions(Request $request, $visible = true) {

		$this->validate($request, [
			'index_shops' => 'required|integer|min:0',
			'index_donation_options' => 'required|integer|min:0',
			'count' => 'required|integer|min:1',
			'filter' => 'array'
		]);

		$index_shops = $request->input('index_shops');
		$index_donation_options = $request->input('index_donation_options');

		$options = $this->retrieveOptions(
			$request->input('filter', []),
			$index_shops,
			$index_donation_options,
			$request->input('count'),
			$totalOptions, $visible);

		return response()->json([
			'options' => $options,
			'count' => $totalOptions,
			'donationOptionSkip' => $index_donation_options,
			'shopSkip' => $index_shops
		]);
	}

	/**
	 * Fetch the next count element from sorted union of shops and donatons options
	 * knowing that we have seen index_shops shops and index_donation_options options so far, respectively
	 * @param  array   $filter                  [description]
	 * @param  [type]  &$index_shops            [description]
	 * @param  [type]  &$index_donation_options [description]
	 * @param  [type]  $count                   [description]
	 * @param  [type]  &$total                  [description]
	 * @param  boolean $visible                 [description]
	 * @return [type]                           [description]
	 */
	protected function retrieveOptions(array $filter, &$index_shops, &$index_donation_options, $count, &$total, $visible = true) {

		$shops = $this->shop_service->retrieveShops(
			array_merge($filter, ['with_vouchers' => TRUE]),
			$index_shops,
			$count,
			$total_shops,
			$visible
		);

		$donation_options = $this->donation_service->retrieveOptions(
			$filter,
			$index_donation_options,
			$count,
			$total_donation_options,
			$visible
		);

		$total = $total_donation_options + $total_shops;

		// mark results with the appropriate type so we can distinguish them
		foreach ($donation_options as $donation_option) {
			$donation_option->redeem_type = self::REDEEM_TYPE_DONATING;
		}
		foreach ($shops as $shop) {
			$shop->redeem_type = self::REDEEM_TYPE_BONSUMING;
		}

		$results = array_merge($shops->toArray(), $donation_options->toArray());
		// sort by decreasing popularity
		usort($results, function($a, $b) {
			return intval(array_get($b, 'popularity', 0)) - intval(array_get($a, 'popularity', 0));
		});
		// take the top $count results
		array_splice($results, $count);
		// update how many shops and donation options we've seen so far
		foreach ($results as $res) {
			switch ($res['redeem_type']) {

				case self::REDEEM_TYPE_BONSUMING:
					$index_shops++;
					break;
				case self::REDEEM_TYPE_DONATING:
					$index_donation_options++;
					break;
			}
		}
		return $results;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request, FrontEnd $fe)
	{
		return $this->showEditor($fe, NULL, $request->input('visible', true));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		return $this->save($request);
	}


	protected function makeBackUrl($visible) {

		if ($visible) {
			return action('RedeemController@index');
		} else {
			return action('RedeemController@indexInvisible');
		}
	}


	/**
	 * set the visibility of a shop
	 * @param Request $request    [description]
	 * @param [type]  $shop_id [description]
	 */
	public function setVisibility(Request $request, $donation_id) {

		$this->validate($request, [
			'visible' => 'required|boolean'
		]);

		$this->donation_service->setVisibility($request->input('visible'), $donation_id);
	}



	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(FrontEnd $fe, Request $request, $donation_id)
	{
		$donation = Donation::findOrFail($donation_id);
		return $this->showEditor($fe, $donation, $request->input('visible', true));
	}


	protected function showEditor(FrontEnd $fe, Donation $donation = NULL, $visible = TRUE) {

		$backUrl = $this->makeBackUrl($visible);

		$fe->addVars([
			'donationStoreUrl' => action('RedeemController@store'),
			'donationDeleteUrl' => action('RedeemController@destroy', NULL),
			'donationUpdateUrl' => action('RedeemController@update', NULL),
			'backUrl' => $backUrl,
			'imagePath' => '/media/img',
			'donation' => $donation
		]);
		return view('redeem.edit')->with([
			'backUrl' => $backUrl,
			'donation' => $donation
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		return $this->save($request, $id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->donation_service->destroy($id);
	}


	protected function save(Request $request, $donation_id = NULL) {

		Validator::extend('donation_sizes', function($attribute, $value, $parameter) {
			$value = array_flatten($value);
			foreach ($value as $el) {
				if (!is_int($el) || intval($el) < 1) {
					return false;
				}
			}
			return true;
		});

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'description' => 'required',
			'thumbnail' => 'required',
			'popularity' => 'required|integer|between:0,100',
			'donation_sizes' => 'required|array|min:1|donation_sizes',
			'tags' => 'array'
		]);

		// let's make sure

		if ($validator->fails()) {
			return response()->json($validator->messages(), 422);
		}

		if ($request->get('validate')) {
			return;
		}

		$fields = $request->only([
			'name', 'description', 'donation_sizes', 'tags',
			'thumbnail', 'thumbnail_mouseover', 'visible', 'popularity']);

		$this->donation_service->save($fields, $donation_id);

		if (!$request->ajax()) {
			return redirect()->action('RedeemController@index');
		}

	}

	public function getVoucher(Guard $auth, Request $request, Bonets $bonets, $shop_id) {

		$this->validate($request, [
			'amount' => 'required|integer|min:1|max:'. $bonets->fromBonets($auth->user()->bonets)
		]);

		$amount = intval($request->input('amount'));

		list($voucher_code, $shop) = $this->shop_service->getVoucher($amount, $shop_id);
		if (!$voucher_code) {
			throw new \Exception('could not retrieve voucher for shop ' . $shop_id . ' with amount ' . $amount);
		}

		$this->dispatch(new RedeemBonets($auth->user()->id, $request->input('amount'), $shop, $voucher_code));

		return $voucher_code;
	}

	public function donate(Guard $auth, Request $request, $option_id) {

		$this->validate($request, [
			'bonets' => 'required|integer|min:1|max:'. $auth->user()->bonets
		]);

		$option = Donation::where('_id', '=', $option_id)->where('visible', '=', true)->firstOrFail();

		$this->dispatch(new DonateBonets($auth->user()->id, $request->input('bonets'), $option));
	}


}
