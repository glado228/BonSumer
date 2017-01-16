<?php namespace Bonsum\Providers;

use Illuminate\Support\ServiceProvider;
use Bonsum\Services\Bonets;

class BonetsServiceProvider extends ServiceProvider {

	protected $defer = true;


	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('bonets', function() {
			return new Bonets();
		});

		$this->app->alias('bonets', 'Bonsum\Services\Bonets');
	}


	public function provides() {
		return ['Bonsum\Services\Bonets', 'bonets'];
	}
}
