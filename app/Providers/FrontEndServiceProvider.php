<?php namespace Bonsum\Providers;

use JavaScript;
use Illuminate\Support\ServiceProvider;
use Bonsum\Services\FrontEnd;
use \Illuminate\Auth\Guard;


class FrontEndServiceProvider extends ServiceProvider {

	// CSS that will by default go into the head section
	protected static $defaultCSS = [];

	// JS that will by default go into the head section
	protected static $defaultHeadScripts = [];

	protected static $defaultScripts = [];

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot(Frontend $fe, Guard $guard)
	{

		// make sure variables are rendered
		$this->app['events']->listen('composing: layouts.master', function() use ($fe, $guard) {

			// Variables used in all contexts
			$fe->addVars([
				'resourceTypes' => \Bonsum\Services\Resource::getTypes(),
				'refreshSession' => action('HomeController@refreshSession'),
				'loginUrl' => action('Auth\AuthController@getLogin'),
				'logoutUrl' => action('Auth\AuthController@postLogout')
			]);

			/**
			 * TO DO: we need to pass text resources to the front end, whether we are in admin mode or not.
			 *  Hooking into LR() is not a solution, because some resources might be needed by the JS frontend
			 *  but not be included in the HTML
			 *  On the other hand, callling addResource for every needed resources (like below) is not a solution either
			 *
			 *  There should be a way to at least inject entire resource files i.e.
			 *
			 *  addResource('general')
			 *
			 *  or even all of them
			 *
			 *  addResourceAll()
			 */
			$fe->addResource([
				'general.ok' => trans('general.ok'),
				'general.cancel' => trans('general.cancel'),
				'general.really_navigate_away' => trans('general.really_navigate_away'),
				'general.error_occurred' => trans('general.error_occurred'),
				'general.please_try_again' => trans('general.please_try_again'),
				'general.please_wait' => trans('general.please_wait')
			], \Bonsum\Services\Resource::RESOURCE_TYPE_TEXT);

			if ($guard->user() && $guard->user()->admin) {

				$fe->addScript('admin-bundle.js');
				$fe->addScript('ckeditor/ckeditor.js');
				$fe->addInlineScript("
					CKEDITOR.disableAutoInline = true;
				");

				$fe->addVars(
					[
					'updateResourceUrl' => action('ResourceController@postUpdateResources')
					]
				);

			    $fe->addCss([
			    	'admin-bundle.css'
			    ]);

			} else {

				$fe->addScript('bundle.js');
			    $fe->addCss([
			    	'bundle.css'
			    ]);
			}

			$fe->addVars(
				[
				'getUserUrl' => action('Auth\AuthController@getUser'),
				'currentUser' => $guard->user()
				]
			);

		});

		// make sure head scripts and css are included
		$this->app['view']->composer('layouts.master', function($view) use ($fe) {
			$view->with($fe->makeHead());
		});

		// make sure footer scripts and css are included
		$this->app['view']->composer('partials.footer', function($view) use ($fe) {
			$view->with($fe->makeFooter());
		});

		// Head css and scripts
		$fe->addCss(self::$defaultCSS);
		$fe->addScript(self::$defaultScripts);
		$fe->addScript(self::$defaultHeadScripts, TRUE);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('frontend', function() {
			return new FrontEnd();
		});

		$this->app->alias('frontend', 'Bonsum\Services\FrontEnd');
	}

}
