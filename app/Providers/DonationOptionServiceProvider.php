<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;

class DonationOptionServiceProvider extends ServiceProvider {



	protected $defer = true;

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('donation', function() {
			return new ShopService($this->app['localization']);
		});

		$this->app->alias('donation', 'Bonsum\Services\Donation');
	}


	public function provides() {
		return ['donation', 'Bonsum\Services\Donation'];
	}

}
