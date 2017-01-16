<?php namespace Bonsum\Http\Controllers;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;

use Bonsum\Services\FrontEnd;

use Illuminate\Http\Request;

class LexiconController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(FrontEnd $fe)
	{

		$lexicon_categories = trans('lexicon.categories');

	    $fe->addVars([
	    	'lexicon_categories' => $lexicon_categories
	    ]);

	    return view('lexicon.index')->with([
	    	'lexicon_categories' => $lexicon_categories
	    ]);
	}

}
