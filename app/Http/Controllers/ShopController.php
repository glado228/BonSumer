<?php namespace Bonsum\Http\Controllers;

use Bonsum\Http\Requests;
use Bonsum\Services\MerchantTransactions;
use Bonsum\Http\Controllers\Controller;
use Bonsum\MongoDB\Shop;
use Bonsum\Services\FrontEnd;
use Carbon\Carbon;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Validator;
use App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Bonsum\Services\Shop as ShopService;
use Jenssegers\Mongodb\Eloquent\Builder as MongoEloquentBuilder;

class ShopController extends Controller {


	const INITIALLY_LOADED_SHOPS = 18;

	public function __construct(ShopService $shop_service) {
		$this->middleware('auth', ['only' => 'redirect']);
		$this->middleware('admin', ['except' => ['index', 'fetch', 'redirect']]);
		$this->shop_service = $shop_service;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, Frontend $fe, Guard $guard)
	{
		return $this->showShops($request, $fe, $guard, true);
	}

	public function indexInvisible(Request $request, Frontend $fe, Guard $guard)
	{
		return $this->showShops($request, $fe, $guard, false);
	}


	protected function showShops(Request $request, Frontend $fe, Guard $guard, $visible = true) {

		/*
			Don't need to preload the shops, they will be loaded by the JS frontend

		 $shops = $this->shop_service->retrieveShops(
			$request->all(),
			0, self::INITIALLY_LOADED_SHOPS, $totalShops, $visible);*/

		$admin_view = $guard->user() && $guard->user()->admin;

		$fe->addVars([
			'shopRedirectUrl' => action('ShopController@redirect', NULL),
			'shopFetchUrl' => action('ShopController@fetch' . (!$visible ? 'Invisible' : '')),
			/*
				we pass initalSearchString to allow the JS frontend to initialize the filter with information
				passed from a normal query string (i.e. without hashbang)
				this is an ugly hack, ideally we would like to use html5 mode in AngularJS, but that cuases
				other problems with existing links
			 */
			'initialSearchString' => $request->input('searchString'),
			//'shops' => $shops,
			//'totalShops' => $totalShops,
			'shopCriteriaMap' => array_flip(Shop::$shopCriteria)
		]);

		if ($admin_view) {
			$fe->addVars([
				'shopDeleteUrl' => action('ShopController@destroy', NULL),
				'shopSetVisibilityUrl' => action('ShopController@setVisibility', NULL)
			]);
		}

		return view('shops.index')->with([
			'visible' => $visible,
			'title_tag' => trans('seo.shops.index.title_tag', ['search' => $request->input('searchString') ?: 'Bonsum']),
			'meta_description' => trans('seo.shops.index.meta_description', ['search' => $request->input('searchString') ?: 'Bonsum'])
		]);
	}


	public function fetch(Request $request) {
		return $this->fetchShops($request, true);
	}

	public function fetchInvisible(Request $request) {
		return $this->fetchShops($request, false);
	}

	protected function fetchShops(Request $request, $visible = true) {

		$this->validate($request, [
			'index' => 'required|integer|min:0',
			'count' => 'required|integer|min:1',
			'filter' => 'array'
		]);

		$shops = $this->shop_service->retrieveShops(
			$request->input('filter', []),
			$request->input('index'),
			$request->input('count'),
			$totalShops,
			$visible
		);

		return response()->json([
			'shops' => $shops,
			'count' => $totalShops
		]);
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
			return action('ShopController@index');
		} else {
			return action('ShopController@indexInvisible');
		}
	}


	/**
	 * set the visibility of a shop
	 * @param Request $request    [description]
	 * @param [type]  $shop_id [description]
	 */
	public function setVisibility(Request $request, $shop_id) {

		$this->validate($request, [
			'visible' => 'required|boolean'
		]);

		$this->shop_service->setVisibility($request->input('visible'), $shop_id);
	}



	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(FrontEnd $fe, Request $request, $shop_id)
	{
		$shop = Shop::findOrFail($shop_id);
		return $this->showEditor($fe, $shop, $request->input('visible', true));
	}


	protected function showEditor(FrontEnd $fe, Shop $shop = NULL, $visible = TRUE) {

		$backUrl = $this->makeBackUrl($visible);


		$fe->addVars([
			'shopStoreUrl' => action('ShopController@store'),
			'shopDeleteUrl' => action('ShopController@destroy', NULL),
			'shopUpdateUrl' => action('ShopController@update', NULL),
			'addVoucherUrl' => action('ShopController@addVouchers', NULL),
			'deleteVoucherUrl' => action('ShopController@deleteVoucher', NULL),
			'backUrl' => $backUrl,
			'imagePath' => '/media/img',
			'shop' => ($shop ? $shop->toArray(false, true) : null),
			'affiliates' => array_merge([''], Shop::$affiliates),
			'rewardTypes' => [
				[
					'label' => 'No reward',
					'value' => Shop::REWARD_TYPE_NO_REWARD
				],
				[
					'label' => 'Proportional',
					'value' => Shop::REWARD_TYPE_PROPORTIONAL
				],
				[
					'label' => 'Fixed per purchase',
					'value' => Shop::REWARD_TYPE_FIXED
				]
			],
			'REWARD_TYPE_PROPORTIONAL' => Shop::REWARD_TYPE_PROPORTIONAL,
			'REWARD_TYPE_FIXED' => Shop::REWARD_TYPE_FIXED,
			'shopTypes' => array_map(function($value) {
				return trans('shop.type.'.$value);
			}, Shop::$shopTypes),
			'shopCriteria' => array_map(function($value) {
				return trans('shop.shop_criteria.'.$value);
			}, Shop::$shopCriteria)
		]);
		return view('shops.edit')->with([
			'backUrl' => $backUrl,
			'shop' => $shop
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
		$this->shop_service->destroy($id);
	}

	public function deleteVoucher(Request $request, $shop_id) {

		$this->validate($request, [
			'code' => 'required'
		]);

		$this->shop_service->deleteVoucher($request->input('code'), $shop_id);
	}

	public function addVouchers(Request $request, $shop_id) {

		$this->validate($request, [
			'codes' => 'required|array',
			'value' => 'required|integer|min:1'
		]);

		if ($request->input('validate')) {
			return;
		}

		$this->shop_service->addVouchers($request->input('codes'), $request->input('value'), $shop_id);
	}


	protected function save(Request $request, $shop_mongo_id = NULL) {

		// $shop_id is the mongo ID, different from $shop->shop_id (numeric, used internally)

		$validator = Validator::make($request->all(), [
			'affiliate' => 'sometimes|in:'. implode(Shop::$affiliates, ','),
			'name' => 'required',
			'shop_id' => ($shop_mongo_id ? 'required|integer|min:1' : 'integer|min:1'),
			'description' => 'required',
			'thumbnail' => 'required',
			'shop_type' => 'required|array',
			'shop_criteria' => 'array',
			'reward_type' => 'required|in:'. implode([Shop::REWARD_TYPE_FIXED, Shop::REWARD_TYPE_PROPORTIONAL, Shop::REWARD_TYPE_NO_REWARD], ','),
			'popularity' => 'required|integer|between:0,100',
			'link' => 'required|url',
			'fixed_reward' => 'required_if:reward_type,'.Shop::REWARD_TYPE_FIXED.'|integer|min:0',
			'proportional_reward' => 'required_if:reward_type,'.Shop::REWARD_TYPE_PROPORTIONAL.'|integer|min:0',
			'tags' => 'string'
		]);

		$custom_messages = [];


		// let's make sure
		// that the internal shop_id unique
		if ($request->has('shop_id')) {
			$query = Shop::where('shop_id', '=', intval($request->input('shop_id')));
			if ($shop_mongo_id) {
				$query->where('_id', '!=', $shop_mongo_id);
			}
			if ($query->count() > 0) {
				$custom_messages['shop_id'] = ['A shop with this ID already exists'];
			}
		}

		if ($request->has('shop_criteria')) {
			foreach (array_keys($request->get('shop_criteria')) as $criterion) {
				if (!in_array($criterion, array_keys(Shop::$shopCriteria))) {
					$custom_messages['shop_criteria'] = ['The criteria are not valid'];
					break;
				}
			}
		}

		if ($validator->fails() || !empty($custom_messages)) {
			return response()->json($validator->messages()->merge($custom_messages), 422);
		}

		if ($request->get('validate')) {
			return;
		}

		$fields = $request->only(['shop_id',
			'name', 'description', 'affiliate', 'reward_type', 'fixed_reward', 'proportional_reward',
			'shop_criteria', 'shop_type', 'link', 'tags',
			'thumbnail', 'thumbnail_mouseover', 'visible', 'popularity']);

		$this->shop_service->save($fields, $shop_mongo_id);

		if (!$request->ajax()) {
			return redirect()->action('ShopController@index');
		}

	}

	public function redirect(MerchantTransactions $ts, Guard $auth, $shop_id) {

		$shop = Shop::find($shop_id);

		if (!$shop) {
			$shop = Shop::where('shop_id', '=', intval($shop_id))->first();
		}

		if (!$shop) {
			throw new NotFoundHttpException('No shop with ID '. $shop_id .' could be found');
		}

		$url = $shop->link;

		if ($shop->affiliate) {
			$driver = $ts->getDriver($shop->affiliate);
			$url = $driver->makeSubIDLink($url, $shop->shop_id, $auth->user()->id);
		}

		return redirect($url);
	}


}
