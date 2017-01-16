<?php namespace Bonsum\Http\Controllers;

use Bonsum\Http\Requests;
use Bonsum\Services\FrontEnd;
use Bonsum\User;
use Illuminate\Auth\Guard;
use Bonsum\Commands\UpdateUserPersonalData;
use Bonsum\Commands\ChangePassword;
use Bonsum\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use Validator;
use Bonsum\Helpers\Csv;
use Bonsum\Helpers\Mail as MailHelper;
use Bonsum\Services\Localization;
use Auth;
use App;

class AccountController extends Controller {

	public function __construct() {

		$this->middleware('auth');
		$this->middleware('admin', ['only' => ['indexAdmin', 'fetchHistoryAdmin', 'updateInfoAdmin']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(FrontEnd $fe, Guard $auth)
	{
		$fe->addVars([
			'fetchHistoryURL' => action('AccountController@fetchHistory'),
			'updateInfoURL' => action('AccountController@updateInfo'),
			'sendInviteEmailURL' => action('AccountController@sendInviteEmail'),
			'updatePasswordURL' => action('AccountController@updatePassword')
		]);

		return $this->showIndex($fe, $auth, $auth->user());
	}


	public function indexAdmin(FrontEnd $fe, Guard $auth, $user_id)
	{
		$user = User::findOrFail($user_id);

		$fe->addVars([
			'fetchHistoryURL' => action('AccountController@fetchHistoryAdmin', ['user_id' => $user_id]),
			'updateInfoURL' => action('AccountController@updateInfoAdmin', ['user_id' => $user_id])
		]);

		return $this->showIndex($fe, $auth, $user);
	}

	protected function showIndex(FrontEnd $fe, Guard $auth, User $user) {

		$fe->addVars([
		 	'user' => $user
		]);

		$fe->addResource([
			'account.link_copied_to_clipboard' => trans('account.link_copied_to_clipboard'),
			'account.invitation_sent' => trans('account.invitation_sent'),
			'account.information_updated' => trans('account.information_updated'),
			'account.link_copy_error' => trans('account.link_copy_error'),
			'account.refer_friends_msg_content' => trans('account.refer_friends_msg_content')
		], \Bonsum\Services\Resource::RESOURCE_TYPE_TEXT);

		return view('account.main')->with([
			'editingOtherUser' => $user->id != $auth->user()->id,
			'user' => $user
		]);
	}

	public function fetchHistory(Request $request, Guard $auth) {

		return $this->fetchHistoryItems($request, $auth->user());
	}


	public function fetchHistoryAdmin(Request $request, $user_id) {

		return $this->fetchHistoryItems($request, User::findOrFail($user_id));
	}

	protected function fetchHistoryItems(Request $request, User $user) {

		$this->validate($request, [
			'filter' => 'required|array',
			'filter.type' => 'required|in:donations,bonets,vouchers',
			'index' => 'integer:min:0',
			'count' => 'required_without:csv|integer:min:1'
		]);

		$filter = $request->input('filter');

		$items = null;
		switch ($filter['type']) {
			case 'bonets':
				$items = $user->bonets_credits->merge($user->merchant_transactions);
				break;
			case 'donations':
				$items = $user->bonets_donations;
				break;
			case 'vouchers':
			default:
				$items = $user->bonets_redeems;
				break;
		}

		$index = $request->input('index', 0);
		$count = $request->input('count');

		$available_bonets = $user->bonets;
		$pending_bonets = 0;
		if ($index === 0) {

			$user->merchant_transactions->each(function ($tr) use (&$pending_bonets) {

				if ($tr->internal_status === \Bonsum\MerchantTransaction::STATUS_OPEN) {
					$pending_bonets += $tr->bonets;
				}
			});
		}

		$items->sortBy(function($value) {
			return ($value->clickdate ?: $value->date);
		}, SORT_REGULAR, true);

		if ($request->input('csv')) {

			$columns = [];

			switch ($filter['type']) {
			case 'bonets':
				$columns = [
				'date',
				'description',
				'amount',
				'currency',
				'bonets',
				'status'
				];
				break;
			case 'donations':
				$columns = [
				'date',
				'receiver',
				'amount',
				'currency',
				'bonets',
				];
				break;
			case 'vouchers':
			default:
				$columns = [
				'date',
				'description',
				'amount',
				'currency',
				'bonets',
				'voucher_code'
				];
				break;
			}

			return Csv::sendCsvFileFromCollection($items, $filter['type'], $columns);

		} else {

			return response()->json(
				[
					'count' => $items->count(),
					'items' => $items->slice($index, $count),
					'overview' => ($index === 0 ? [
						'total' => $available_bonets + $pending_bonets,
						'available' => $available_bonets,
						'pending' => $pending_bonets
					] : NULL)
				]
			);
		}
	}

	public function updatePassword(Request $request, Guard $auth) {

		Validator::extend('is_correct_password',
            function($attribte, $value) use ($auth) {
                return Hash::check($value, $auth->user()->password);
            }
        );

        $rules = array(
                'current_password' => 'required|is_correct_password',
    			'new_password' => 'required|min:6|confirmed',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
			return response()->json($validator->messages(), 422);
		}

		if ($request->input('validate')) {
			return;
		}

		$this->dispatch(new ChangePassword($auth->user(), $request->input('new_password')));

		return response()->json([
			'user' => $auth->user()
		]);
	}


	public function updateInfo(Request $request, Guard $auth) {

		$user = $auth->user();

		return $this->doUpdateInfo($request, $user);
	}

	public function updateInfoAdmin(Request $request, $user_id) {

		$user = User::findOrFail($user_id);

		return $this->doUpdateInfo($request, $user);
	}

	public function sendInviteEmail(Request $request, Localization $localization) {

		$this->validate($request, [
			'email' => 'required|email',
			'message' => 'required',
		]);

        if ($request->input('validate')) {
            return;
        }

        $user = Auth::user();
        MailHelper::mailUser($user, 'invite',
        	['account.refer_friends_msg_subject', ['name' => $user->firstname]],
        	[
	        	'invite_url' => FALSE, // this would usually appear in th footer but we don't want that in this case
	        	'email_invite_url' => $localization->getInviteUrl($user),
	        	'personal_msg' => $request->input('message')
        	],
        	App::getLocale(),
        	$request->input('email'));
	}

	protected function doUpdateInfo(Request $request, User $user) {

		$this->validate($request, [
			'firstname' => 'required|max:200',
			'lastname' => 'max:200',
			'gender' => 'in:M,F'
		]);

		if ($request->input('validate')) {
			return;
		}

		$this->dispatch(new UpdateUserPersonalData($user, $request->only([
			'firstname',
			'lastname',
			'gender'
		])));

		$user = $user->fresh();

		return response()->json([
			'user' => $user
		]);
	}

}
