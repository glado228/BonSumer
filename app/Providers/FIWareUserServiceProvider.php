<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;

class FIWareUserServiceProvider extends ServiceProvider {


	protected $defer = true;

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(
			'Bonsum\Services\FIWareUser',
			function($app) {
		 		return new \Bonsum\Services\FIWareUser(
		 			$app['auth.driver'],
		 			$app['Bonsum\Services\Registrar']
		 		);
			}
		);
	}

	/**
	 * What we are providing
	 * @return array what abstract types we are providing
	 */
	public function provides() {
		return ['Bonsum\Services\FIWareUser'];
	}

}
