<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\FiWareAuth;
use Bonsum\Services\Localization;
use \Illuminate\Auth\Guard;
use Request;
use Log;
use App;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot(Guard $guard, Localization $localization)
	{
		/**
		 * variable injected in all views
		 */
		$this->app['view']->composer('*', function($view) use ($guard) {

			$view->with([
				'adminMode' => $guard->check() && $guard->user()->admin,
				'locale' => $this->app->getLocale()
			]);

		});

		/**
		 * use backgoround image for authentication pages.
		 */
		$this->app['view']->composer('auth.*', function($view) {
			$view->with('bonsum_background_image', TRUE);
		});

		/**
		 * Provide the toolbar with information about the available locales,
		 * for the language selector dropdown.
		 */
		$this->app['view']->composer('partials.toolbar', function($view) use ($localization) {

			$locales = config('app.available_locales');
			$locale_hostnames = [];

			foreach ($locales as $locale) {
				$locale_hostnames[$locale] = $localization->getHostName($locale);
			}

			$view->with([
				'locales'=> $locales,
				'locale_hostnames'=>$locale_hostnames,
				'current_locale' => App::getLocale()
			]);
		});

		/**
		 *	Inject available locales in the admin tool bar view
		 */
		$this->app['view']->composer('admin.partials.admintoolbar', function($view) {

			$locales = config('app.available_locales');
			$view->with('available_locales', array_combine($locales, array_map('strtoupper', $locales)));
		});

		/**
		 *  Extend the SocialiteManager to accommodate OAuth2 FiWare authentication
		 */
		$this->app['Laravel\Socialite\Contracts\Factory']->extend('FIWare', function($app) {

	        $config = $app['config']['services.fiware'];

	        return $app['Laravel\Socialite\Contracts\Factory']->buildProvider('Bonsum\Services\FIWareAuth', $config);
		});
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind(
			'Illuminate\Contracts\Auth\Registrar',
			'Bonsum\Services\Registrar'
		);
	}

}
