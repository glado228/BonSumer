<?php namespace Bonsum\Http\Controllers;

use Bonsum\Http\Requests;
use Bonsum\Http\Controllers\Controller;
use Bonsum\MongoDB\Article;
use Bonsum\Services\Localization;

use Bonsum\Services\FrontEnd;

use Carbon\Carbon;
use Bonsum\Services\Article as ArticleService;

use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException as BadRequestHttpException;

class ArticleController extends Controller {

	const INITIALLY_LOADED_ARTICLES = 9;


	public function __construct(ArticleService $article_service) {
		$this->middleware('admin', ['except' => ['show', 'index', 'fetch']]);
		$this->article_service = $article_service;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, FrontEnd $fe, Guard $guard)
	{
		return $this->showArticles($request, $fe, $guard, true);
	}

	public function indexInvisible(Request $request, FrontEnd $fe, Guard $guard)
	{
		return $this->showArticles($request, $fe, $guard, false);
	}


	public function fetch(Request $request) {

		return $this->fetchArticles($request, true);
	}


	public function fetchInvisible(Request $request) {

		return $this->fetchArticles($request, false);
	}


	/*
		Ajax endpoint for fetching articles
	 */
	public function fetchArticles(Request $request, $visible) {

		$this->validate($request, [
			'index' => 'required|integer|min:0',
			'count' => 'required|integer|min:1',
			'filter' => 'array|required'
		]);

		$articles = $this->article_service->retrieveArticles(
			$request->input('filter'),
			$request->input('index'),
			$request->input('count'),
			$totalArticles,
			$visible
		);

		return response()->json([
			'articles' => $articles,
			'count' => $totalArticles
		]);
	}

	protected function showArticles(Request $request, FrontEnd $fe, Guard $guard, $visible=true) {

		$articles = $this->article_service->retrieveArticles(
			$request->all(),
			0, self::INITIALLY_LOADED_ARTICLES,
			$totalArticles,
			$visible
		);

		$admin_view = $guard->user() && $guard->user()->admin;

		$fe->addVars([
			'articleFetchUrl' => action('ArticleController@fetch'. (!$visible ? 'Invisible' : '')),
			'articles' => $articles,
			'currentFilter' => $request->all(),
			'totalArticles' => $totalArticles,
			'sortingOptions' => [
				[
					'label' => trans('article.newest_first'),
					'value' => 'newest_first'
				],
				[
					'label' => trans('article.oldest_first'),
					'value' => 'oldest_first'
				]
			]
		]);

		if ($admin_view) {
			$fe->addVars([
				'articleDeleteUrl' => action('ArticleController@destroy', NULL),
				'articleSetVisibilityUrl' => action('ArticleController@setVisibility', NULL)
			]);
		}

		return view('magazine.index')->with([
			'visible' => $visible,
			'title_tag' => trans('article.index.title_tag'),
			'meta_description' => trans('article.index.meta_description'),
			'share_tags' => true
		]);
	}


	/**
	 * Saves a new article or updates an existing one
	 * @param  Request $request    original HTTP request
	 * @param  int|NULL  			$article_id  id of article to update. if Null, a new article will be created
	 * @return HTTP response
	 */
	protected function save(Request $request, $article_id = NULL) {


		$this->validate($request, [
			'title' => 'required',
			'description' => 'required',
			'url_friendly_title' => 'alpha_dash',
			'body' => 'required|noscripttags|required_without:validate',
			'date' => 'required|date',
			'tags' => 'array|max:10',
			'authors' => 'required|array|between:1,5',
			'meta_description' => 'string|max:156'
		]);

		if ($request->input('validate')) {
			return;
		}

		$fields = $request->only(['title', 'title_tag', 'meta_description', 'description', 'body', 'image', 'thumbnail', 'date', 'tags', 'authors', 'visible', 'url_friendly_title']);
		$this->article_service->save($fields, $article_id);

		if (!$request->ajax()) {
			return redirect()->action('ArticleController@index');
		}
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
			return action('ArticleController@index');
		} else {
			return action('ArticleController@indexInvisible');
		}
	}


	public function showInvisible(Guard $auth, Request $request, $article_id) {

		return $this->showArticle($auth, $article_id, false);
	}

	public function show(Guard $auth, Request $request, $article_id) {

		return $this->showArticle($auth, $article_id, true);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int|string  $article_id Mongo ID or url_friendly_title
	 * @return Response
	 */
	public function showArticle(Guard $auth, $article_id, $only_visible = true)
	{
		$article = $this->article_service->fetchArticle($article_id, $auth->user() && $auth->user()->admin);
		if (!$article || ($only_visible && !$article->visible)) {
			throw new NotFoundHttpException();
		}

		return view('magazine.article')->with([
			'article' => $article,
			'backUrl' => $this->makeBackUrl($article->visible),
			'title_tag' => $article->title_tag,
			'meta_description' => $article->meta_description,
			'share_tags' => true
		]);
	}

	/**
	 * set the visibility of an article
	 * @param Request $request    [description]
	 * @param [type]  $article_id [description]
	 */
	public function setVisibility(Request $request, $article_id) {

		$this->validate($request, [
			'visible' => 'required|boolean'
		]);

		$this->article_service->setVisibility($request->input('visible'), $article_id);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request, Localization $localization, FrontEnd $fe)
	{
		return $this->showEditor($localization, $fe, NULL, $request->input('visible', true));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Request $request, Localization $localization, FrontEnd $fe, $article_id)
	{
		$article = Article::findOrFail($article_id);
		if ($article->locale !== App::getLocale()) {
			throw new \Exception('Article should be edited in its original locale');
		}
		return $this->showEditor($localization, $fe, $article, $request->input('visible', true));
	}

	/**
	 * Display the view to edit an article
	 * @param  FrontEnd $fe      frontend service
	 * @param  Article  $article article model or NULL for new article
	 * @param  boolean $visible if we are coming from a visible article list
	 * @return Response
	 */
	protected function showEditor(Localization $localization, FrontEnd $fe, Article $article = NULL, $visible = TRUE) {

		$backUrl = $this->makeBackUrl($visible);

		if ($article) {
			$article_fields = $article->toArray(true);
		} else {
			$article_fields = NULL;
		}

		$fe->addScript('ckeditor/ckeditor.js');
		$fe->addInlineScript("
			CKEDITOR.replace('body', {
				language: '". $localization->getLang(). "',
				height: '300px',
				removeButtons: null,
				removeDialogTabs: null,
				extraPlugins: 'justify,oembed,language',
				contentsCss: '/css/". $fe->fromManifest('admin-bundle.css') ."'
			});
		");

		$fe->addVars([
			'articleStoreUrl' => action('ArticleController@store'),
			'articleDeleteUrl' => action('ArticleController@destroy', NULL),
			'articleUpdateUrl' => action('ArticleController@update', NULL),
			'backUrl' => $backUrl,
			'imagePath' => '/media/img',
			'article' => $article_fields
		]);
		return view('magazine.edit')->with([
			'date_format' => 'yyyy-MM-dd',
			'backUrl' => $backUrl,
			'article' => $article
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
		Article::destroy($id);
	}

}
