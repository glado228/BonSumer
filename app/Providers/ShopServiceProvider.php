<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\Shop as ShopService;

class ShopServiceProvider extends ServiceProvider {


	protected $defer = true;

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('shop', function() {
			return new ShopService($this->app['localization']);
		});

		$this->app->alias('shop', 'Bonsum\Services\Shop');
	}


	public function provides() {
		return ['shop', 'Bonsum\Services\Shop'];
	}
}
