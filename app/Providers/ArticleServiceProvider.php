<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\Article as ArticleService;

class ArticleServiceProvider extends ServiceProvider {

	protected $defer = true;

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('article', function() {
			return new ArticleService($this->app['localization']);
		});

		$this->app->alias('article', 'Bonsum\Services\Article');
	}


	public function provides() {
		return ['article', 'Bonsum\Services\Article'];
	}

}
