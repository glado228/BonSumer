<?php namespace Bonsum\Http\Controllers;

use Illuminate\Http\Request;
use Bonsum\Http\Controllers\Controller;
use Bonsum\MongoDB\Article;
use Bonsum\Services\FrontEnd;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Bonsum\Services\Article as ArticleService;
use App;

class HomeController extends Controller {

	/**
	 * Show the home page.
	 *
	 * @return Response
	 */
	public function index(FrontEnd $fe, $article_title = null)
	{
		if ($article_title) {
			/*
				If we are here, somebody entered a URL like

					www.bonsum.de/the-new-cool-article-about-sustainable-living

				the-new-cool-article-about-sustainable-living refers to an old article from the
				WP site that has to be made visible if it has the "available_top_level" flag

				We therefore redirect to the canonical article URL if this is the case
			 */

			if (Article::where('url_friendly_title', '=', $article_title)
				->where('available_top_level', '=', true)->exists())
			{
				return redirect()->action('ArticleController@show', $article_title, 301);
			}

			throw new NotFoundHttpException();
		}

		$fe->addVars([
			'shopUrl' => action('ShopController@index'),
			'redeemUrl' => action('RedeemController@index')
		]);

		return view('home');
	}


	public function refreshSession() {
	}

	/**
	 * Generate a dynamic sitemap for the site
	 * @return [type] [description]
	 */
	public function sitemap(ArticleService $ArticleService) {

		$sitemap = app('sitemap');

		$sitemap->add(action('HomeController@index'), null, 1.0, 'weekly');
		$sitemap->add(action('ShopController@index'), null, 0.9, 'weekly');
		$sitemap->add(action('RedeemController@index'), null, 0.9, 'weekly');
		$sitemap->add(action('ArticleController@index'), null, 0.9, 'weekly');

		$sitemap->add(action('StaticController@redeemVouchers'), null, 0.8, 'weekly');
		$sitemap->add(action('StaticController@donateBonets'), null, 0.8, 'weekly');
		$sitemap->add(action('StaticController@forest'), null, 0.8, 'weekly');

		$sitemap->add(action('StaticController@howto'), null, 0.7, 'weekly');

		$sitemap->add(action('StaticController@about'), null, 0.6, 'weekly');
		$sitemap->add(action('LexiconController@index'), null, 0.6, 'weekly');

		$sitemap->add(action('StaticController@join'), null, 0.5, 'weekly');

		$sitemap->add(action('StaticController@shopOwners'), null, 0.4, 'weekly');

		$sitemap->add(action('StaticController@jobs'), null, 0.3, 'weekly');

		$sitemap->add(action('StaticController@privacy'), null, 0.2, 'weekly');
		$sitemap->add(action('StaticController@press'), null, 0.2, 'weekly');

		$sitemap->add(action('StaticController@faq'), null, 0.1, 'weekly');
		$sitemap->add(action('StaticController@contact'), null, 0.1, 'weekly');
		$sitemap->add(action('StaticController@imprint'), null, 0.1, 'weekly');
		$sitemap->add(action('StaticController@terms'), null, 0.1, 'weekly');

		$articles = $ArticleService->retrieveArticles([]);
		foreach ($articles as $article) {
			$sitemap->add(action('ArticleController@show', [$article->url_friendly_title]), null, 0.1, 'weekly');
		}

		return $sitemap->render('xml');
	}

}
