<?php namespace Bonsum\Http\Controllers\Admin;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;
use Bonsum\Services\FrontEnd;
use Bonsum\User;
use Bonsum\Commands\ActivateUser;
use Bonsum\Commands\DisableUser;
use Bonsum\Commands\DeleteUser;
use Bonsum\Commands\ResetPassword;
use Bonsum\Commands\CreditBonets;
use Bonsum\Commands\UserAdminRightsGrant;
use Bonsum\Commands\UserAdminRightsRevoke;
use Bonsum\Helpers\Csv;
use Bonsum\Commands\SendConfirmationReminder;
use Bonsum\Helpers\GridFilter;

use Illuminate\Http\Request;

class UserController extends Controller {


	const DEFAULT_PAGE_SIZE = 15;

	public function __construct() {

		$this->middleware('admin');
		$this->max_creditable_bonets = config('bonets.max_creditable_bonets', CreditBonets::DEFAULT_MAX_CREDITABLE);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function showUsers(FrontEnd $fe)
	{

		$fe->addVars([
			'users' => [
				'loadURL' => action('Admin\UserController@loadUsers'),
				'singleUserURL' => action('Admin\UserController@showSingleUser', ['user' => NULL]),
				'pageSize' => self::DEFAULT_PAGE_SIZE,
				'locales' => config('app.available_locales')
			]
		]);

		return view('admin.user_list');
	}

	public function loadUsers(Request $request) {

		$this->validate($request, [
			'startRow' => 'integer|required_without:csv',
			'endRow' => 'integer|required_without:csv',
			'sortModel' => 'array',
			'filterModel' => 'array'
		]);

		$users = User::query();

		if ($filterModel = $request->get('filterModel')) {
			foreach ($filterModel as $field => $filter) {

				switch ($field) {

					case 'email':
					case 'firstname':
					case 'lastname':
						GridFilter::text($users, $field, $filter);
						break;
					case 'bonets':
						GridFilter::number($users, $field, $filter);
						break;
					default:
						GridFilter::set($users, $field, $filter);
						break;
				}
			}
		}

		if ($sortModel = $request->input('sortModel')) {
			foreach ($sortModel as $sort) {
				$users->orderBy($sort['field'], $sort['sort']);
			}
		}

		if ($request->input('csv')) {

			return Csv::sendCsvFileFromQuery($users, 'users', [ 'id',
                                'bonets',
                                'email',
                                'firstname',
                                'lastname',
                                'gender',
                                'preferred_locale',
                                'admin',
                                'disabled',
                                'confirmed',
                                'created_at',
                                'disabled_at'
					]);

		} else {

			$start = intval($request->input('startRow'));
			$end = intval($request->input('endRow'));
			$lastRow = $users->count();

			return response()->json([
				'rowsThisPage' => $users->skip($start)->take($end-$start)->get(),
				'lastRow' => $lastRow
			]);
		}
	}

	public function setDisabled($user_id, $disabled) {

		$user = User::findOrFail($user_id);

		if ($disabled) {
			$this->dispatch(new DisableUser($user));
		} else {
			$this->dispatch(new ActivateUser($user));
		}

		return response()->json([
			'user' => $user
		]);
	}

	public function resetPassword($user_id) {

		$user = User::findOrFail($user_id);

		$this->dispatch(new ResetPassword($user));

		return response()->json([
			'user' => $user
		]);
	}

	public function setAdmin($user_id, $admin) {

		$user = User::findOrFail($user_id);

		if ($admin) {
			$this->dispatch(new UserAdminRightsGrant($user));
		} else {
			$this->dispatch(new UserAdminRightsRevoke($user));
		}

		return response()->json([
			'user' => $user
		]);
	}

	public function creditBonets(Request $request, $user_id) {

		$this->validate($request, [
			'bonets' => 'required|integer|min:1|max:' . $this->max_creditable_bonets
		]);

		$this->dispatch(new CreditBonets($user_id, $request->input('bonets'), $request->input('bonets_credit_message')));

		return response()->json([
			'user' => User::findOrFail($user_id)
		]);
	}

	public function sendConfirmationReminder($user_id) {

		$user = User::findOrFail($user_id);

		$this->dispatch(new SendConfirmationReminder($user));
	}

	public function deleteUser($user_id) {

		$user = User::findOrFail($user_id);

		$this->dispatch(new DeleteUser($user));
	}

	public function showSingleUser(FrontEnd $fe, $user_id) {

		$user = User::findOrFail($user_id);

		$fe->addVars([
			'user' => $user,
			'backURL' => action('Admin\UserController@showUsers'),
			'resetPasswordURL' => action('Admin\UserController@resetPassword', ['user' => $user_id]),
			'setAdminURL' => action('Admin\UserController@setAdmin', ['user' => $user_id, 'admin' => NULL]),
			'deleteUserURL' => action('Admin\UserController@deleteUser', ['user' => NULL]),
			'setDisabledURL' => action('Admin\UserController@setDisabled', ['user' => $user_id, 'disabled' => NULL]),
			'creditBonetsURL' => action('Admin\UserController@creditBonets', ['user'=> $user_id]),
			'sendConfirmationReminderURL' => action('Admin\UserController@sendConfirmationReminder', ['user'=> $user_id])
		]);

		return view('admin.user')->with([
			'user' => $user
		]);
	}
}
