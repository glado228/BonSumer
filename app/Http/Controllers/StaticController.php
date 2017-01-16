<?php namespace Bonsum\Http\Controllers;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;

class StaticController extends Controller {
	/**
	 * Controller that is used to display all the static html views
	 *
	 */

	public function about()
	{
		return view('about');
	}

	public function ambassador()
	{
		return view('ambassador');
	}

	public function contact()
	{
		return view('contact');
	}

	public function donateBonets()
	{
		return view('donate-bonets');
	}

	public function faq()
 	{
		return view('faq');
	}

	public function forest()
	{
		return view('forest');
	}

	public function howto()
	{
		return view('howto');
	}

	public function imprint()
	{
		return view('imprint');
	}

	public function jobs()
	{
		return view('jobs');
	}

	public function join()
	{
		return view('join');
	}

	public function press()
	{
		return view('press');
	}

	public function privacy()
	{
		return view('privacy');
	}

	public function redeemVouchers()
	{
		return view('redeem-vouchers');
	}

	public function shopOwners()
	{
		return view('shop-owners');
	}

	public function terms()
	{
		return view('terms');
	}

	public function seo()
	{
		return view('admin.seo');
	}

}
