<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider {

	// list of views with specific SEO tags
	static $seoViews = [
		'home',
		'howto',
		'about',
		'contact',
		'press',
		'shop-owners',
		'lexicon',
		'jobs',
		'privacy',
		'terms',
		'imprint',
		'account.main',
		'faq',
		'magazine.index',
		'join',
		'redeem-vouchers',
		'donate-bonets'
	];


	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$addSeoTags = function($view) {
			$view->with([
				'meta_description' => trans('seo.'.$view->getName(). '.meta_description'),
				'title_tag' => trans('seo.'.$view->getName().'.title_tag')
			]);
		};

		foreach (self::$seoViews as $view) {
			$this->app['view']->composer($view, $addSeoTags);
		}

		$this->app['view']->composer('admin.seo', function($view) {
			$view->with([
				'views' => self::$seoViews
			]);
		});

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
