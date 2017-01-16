<?php namespace Bonsum\Http\Controllers\Auth;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Request;
use Bonsum\Services\FIWareUser;
use Auth;
use Exception;

class FIWareController extends Controller {

	public function __construct() {

		$this->middleware('guest');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getLogin(Socialite $socialite)
	{
		return $socialite->driver('FIWare')->redirect();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCallback(Socialite $socialite, FIWareUser $fiware_user)
	{
		$user = $socialite->driver('FIWare')->user();

		$fiware_user->createIfNewAndLogin($user);

		return redirect()->intended();
	}

}
